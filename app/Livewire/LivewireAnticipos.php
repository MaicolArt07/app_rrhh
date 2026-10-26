<?php

namespace App\Livewire;

use App\Livewire\Form\AnticipoPrestamoForm;
use App\Models\AnticipoPrestamo;
use App\Models\Empleado;
use App\Models\Tipo;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireAnticipos extends Component
{
    use WithPagination;

    public AnticipoPrestamoForm $form;

    public $estadoFiltro = 'activo';
    public string $search = '';
    public $modalVisible = false;
    public bool $buscado = false;

    public $pagoVisible = false;
    public $pagoAnticipoId = null;
    public $montoPago = '';

    public ?int $viewingId = null;

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        $this->form->resetForm();
    }

    public function updatingEstadoFiltro()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function buscar()
    {
        $this->resetPage();
        $this->buscado = true;
    }

    public function limpiarFiltros()
    {
        $this->reset('search', 'estadoFiltro');
        $this->buscado = false;
    }

    public function verDetalle($id)
    {
        $this->viewingId = $id;
    }

    public function cerrarDetalle()
    {
        $this->viewingId = null;
    }

    public function render()
    {
        $anticipos = null;

        if ($this->buscado) {
            $query = AnticipoPrestamo::query()->with(['empleado.persona', 'tipoAnticipo']);

            if (!empty($this->estadoFiltro)) {
                $query->where('estado_anticipo', $this->estadoFiltro);
            }

            if (!empty($this->search)) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('codigo_empleado', 'like', "%{$this->search}%")
                        ->orWhereHas('persona', function ($p) {
                            $p->where('primer_nombre', 'like', "%{$this->search}%")
                                ->orWhere('primer_apellido', 'like', "%{$this->search}%");
                        });
                });
            }

            $anticipos = $query->orderByDesc('id')->paginate(10);
        }

        return view('livewire.anticipos.index', [
            'anticipos' => $anticipos,
            'anticipoDetalle' => $this->viewingId
                ? AnticipoPrestamo::with(['empleado.persona', 'tipoAnticipo', 'pagos'])->find($this->viewingId)
                : null,
            'empleados' => Empleado::with('persona')->where('estado', true)->get(),
            'tiposAnticipo' => Tipo::categoria('tipo_anticipo')->activos()->orderBy('orden')->get(),
        ]);
    }

    public function openModal()
    {
        $this->form->resetForm();
        $this->modalVisible = true;
    }

    public function save()
    {
        $this->validate($this->form->rules(), $this->form->messages());

        try {
            $montoCuota = round($this->form->monto_total / $this->form->numero_cuotas, 2);

            AnticipoPrestamo::create(array_merge($this->form->toArray(), [
                'monto_cuota' => $montoCuota,
                'saldo_pendiente' => $this->form->monto_total,
                'estado_anticipo' => 'activo',
            ]));

            $this->dispatch('toast', type: 'success', message: 'Anticipo/préstamo registrado correctamente.');
            $this->modalVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo registrar el anticipo. Inténtalo nuevamente.');
        }
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $this->form->resetForm();
        $this->modalVisible = false;
    }

    public function abrirPago($id)
    {
        $this->pagoAnticipoId = $id;
        $this->montoPago = '';
        $this->pagoVisible = true;
    }

    public function cerrarPago()
    {
        $this->pagoVisible = false;
        $this->pagoAnticipoId = null;
    }

    public function registrarPago()
    {
        $this->validate([
            'montoPago' => 'required|numeric|min:0.01',
        ]);

        try {
            $anticipo = AnticipoPrestamo::findOrFail($this->pagoAnticipoId);

            if ($this->montoPago > $anticipo->saldo_pendiente) {
                $this->addError('montoPago', 'El monto no puede superar el saldo pendiente (Q ' . number_format($anticipo->saldo_pendiente, 2) . ').');
                return;
            }

            $anticipo->pagos()->create([
                'monto_pagado' => $this->montoPago,
                'fecha_pago' => now()->format('Y-m-d'),
            ]);

            $nuevoSaldo = round($anticipo->saldo_pendiente - $this->montoPago, 2);
            $anticipo->update([
                'saldo_pendiente' => $nuevoSaldo,
                'estado_anticipo' => $nuevoSaldo <= 0 ? 'pagado' : 'activo',
            ]);

            $this->dispatch('toast', type: 'success', message: 'Pago registrado correctamente.');
            $this->pagoVisible = false;
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo registrar el pago.');
        }
    }

    public function cancelar($id)
    {
        try {
            AnticipoPrestamo::findOrFail($id)->update(['estado_anticipo' => 'cancelado']);
            $this->dispatch('toast', type: 'warning', message: 'Anticipo cancelado.');
        } catch (\Throwable $e) {
            $this->dispatch('toast', type: 'error', message: 'No se pudo cancelar el anticipo.');
        }
    }
}
