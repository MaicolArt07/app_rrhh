"""Une las columnas del PDF del Banco Bisa (páginas 1-5 + 34-38) por posición de fila
y lee los correos corporativos del PDF del Foro. Deja todo en informe.json."""
import sys, io, re, json, pymupdf
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding="utf-8")
BASE = r"C:\xampp\htdocs\app_deltax_rrhh\informe-datos"
OUT = r"C:\xampp\htdocs\app_deltax_rrhh\storage\app\importaciones\informe-datos-2026-10.json"

banco = pymupdf.open(BASE + r"\Cuentas Banco Bisa_Otros - cuentas banco bisa.pdf")

def lineas(pagina):
    res = []
    for b in pagina.get_text("dict")["blocks"]:
        for l in b.get("lines", []):
            t = "".join(s["text"] for s in l["spans"]).strip()
            if t:
                res.append((l["bbox"][0], (l["bbox"][1] + l["bbox"][3]) / 2, t))
    return res

COLUMNAS = [("ci", 0), ("nombre", 120), ("estado", 300), ("contrato", 360), ("empresa", 460), ("cuenta", 570)]

def columna(x):
    nombre = COLUMNAS[0][0]
    for c, desde in COLUMNAS:
        if x >= desde:
            nombre = c
    return nombre

def es_cuenta(t):
    return bool(re.fullmatch(r"[\d\s\-]{8,}", t))

def es_telefono(t):
    return "@" not in t and len(re.sub(r"\D", "", t)) >= 7 and not re.search(r"[A-Za-z]{3,}", t)

filas = []
for izq in range(0, 5):
    der = izq + 33
    # agrupar líneas en filas por cercanía vertical (tolerancia 4 pt)
    grupos = []
    for x, y, t in sorted((l for l in lineas(banco[izq]) if l[1] >= 70), key=lambda l: l[1]):
        if grupos and abs(grupos[-1]["y"] - y) <= 4:
            grupos[-1]["lineas"].append((x, t))
        else:
            grupos.append({"y": y, "lineas": [(x, t)]})
    extra = [(x, y, t) for x, y, t in lineas(banco[der]) if y >= 70]
    # una cuenta impresa fuera de su fila (celda con salto de línea) queda sola:
    # se asigna a la fila vecina con nombre que no tenga cuenta
    sueltas = [g for g in grupos if all(columna(x) == "cuenta" for x, t in g["lineas"])]
    for s in sueltas:
        vecinas = sorted((g for g in grupos if g is not s and any(columna(x) == "nombre" for x, t in g["lineas"])
                          and not any(columna(x) == "cuenta" for x, t in g["lineas"])), key=lambda g: abs(g["y"] - s["y"]))
        if vecinas and abs(vecinas[0]["y"] - s["y"]) <= 16:
            vecinas[0]["lineas"].extend(s["lineas"])
            print(f"  (cuenta {s['lineas'][0][1]} reasignada a la fila de y={vecinas[0]['y']:.0f})")
        grupos.remove(s)
    for g in grupos:
        f = {}
        for x, t in sorted(g["lineas"]):
            col = columna(x)
            # una cuenta larga (p. ej. BCP 201-51369314-3-77) invade la columna de empresa
            if col == "empresa" and es_cuenta(t):
                col = "cuenta"
            f[col] = (f[col] + " " + t) if col in f and col != "cuenta" else t
        if "nombre" not in f:
            continue
        y = g["y"]
        cercanos = [t for x, yy, t in extra if abs(yy - y) <= 6]
        correos = re.findall(r"[\w.\-+]+@[\w.\-]+", " ".join(cercanos))
        telefonos = []
        notas = []
        for t in cercanos:
            sin_correo = re.sub(r"[\w.\-+]+@[\w.\-]+[,;]?", "", t).strip()
            if not sin_correo:
                continue
            m = re.match(r"^([\d\s\-\(\)\+]{7,})(.*)$", sin_correo)
            if m and es_telefono(m.group(1)):
                telefonos.append(m.group(1).strip())
                if m.group(2).strip():
                    notas.append(m.group(2).strip())
            else:
                notas.append(sin_correo)
        f["celular"] = " / ".join(telefonos) or None
        f["correo"] = correos[0] if correos else None
        f["nota"] = " ".join(notas) or None
        f["pagina"] = izq + 1
        filas.append(f)

# --- correos corporativos
foro = pymupdf.open(BASE + r"\Correos para Foro - N° 48 - ENVIADO EN EL CALENDARIO.pdf")
texto = "\n".join(p.get_text() for p in foro)
tokens = [t.strip() for t in texto.split("\n") if t.strip()]
corporativos = []
i = 0
while i < len(tokens):
    if re.fullmatch(r"\d{1,2}", tokens[i]) and i + 2 < len(tokens):
        nombre, apellido = tokens[i + 1], tokens[i + 2]
        correo = None
        j = i + 3
        while j < len(tokens) and not re.fullmatch(r"\d{1,2}", tokens[j]) and tokens[j] not in ("Active",) and "@" not in tokens[j] and tokens[j].isupper():
            j += 1
        if j < len(tokens) and "@" in tokens[j]:
            correo = tokens[j].strip()
        corporativos.append({"nombre": f"{nombre} {apellido}".strip(), "correo": correo})
        i += 3
    else:
        i += 1

json.dump({"banco": filas, "corporativos": corporativos}, open(OUT, "w", encoding="utf-8"), ensure_ascii=False, indent=1)
print(f"Banco: {len(filas)} filas | con celular: {sum(1 for f in filas if f['celular'])} | con correo: {sum(1 for f in filas if f['correo'])} | con nota: {sum(1 for f in filas if f['nota'])}")
print(f"Correos corporativos: {len(corporativos)} | sin correo: {[c['nombre'] for c in corporativos if not c['correo']]}")
for f in filas:
    if f.get("estado", "").lower() == "activo":
        print(f"  {f.get('ci',''):16} {f['nombre'][:34]:34} {f.get('empresa','')[:13]:13} {f.get('cuenta','') or '-':20} cel={f['celular']} correo={f['correo']} nota={f['nota']}")

