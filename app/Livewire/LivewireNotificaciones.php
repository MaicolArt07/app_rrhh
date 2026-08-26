<?php

namespace App\Livewire;

use App\Models\Notificacion;
use Livewire\Component;
use Livewire\WithPagination;

class LivewireNotificaciones extends Component
{
    use WithPagination;

    public $soloNoLeidas = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSoloNoLeidas()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Notificacion::where('id_usuario_destino', auth()->id())->with('tipoNotificacion');

        if ($this->soloNoLeidas) {
            $query->where('leido', false);
        }

        return view('livewire.notificaciones.index', [
            'notificaciones' => $query->orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function marcarLeida($id)
    {
        $notificacion = Notificacion::where('id_usuario_destino', auth()->id())->findOrFail($id);
        $notificacion->update(['leido' => true, 'fecha_leido' => now()]);
    }

    public function marcarTodasLeidas()
    {
        Notificacion::where('id_usuario_destino', auth()->id())
            ->where('leido', false)
            ->update(['leido' => true, 'fecha_leido' => now()]);

        $this->dispatch('toast', type: 'success', message: 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
