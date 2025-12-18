<?php

namespace App\Helpers;


use Illuminate\Support\Traits\ForwardsCalls;
use Livewire\Component;
use function Livewire\{store, trigger, wrap };
use ReflectionUnionType;
use Livewire\Mechanisms\Mechanism;
use Livewire\Mechanisms\HandleComponents\Synthesizers\Synth;
use Livewire\Exceptions\PublicPropertyNotFoundException;
use Livewire\Exceptions\MethodNotFoundException;
use Livewire\Drawer\Utils;
use Illuminate\Support\Facades\View;
use \Livewire\Mechanisms\HandleComponents\HandleComponents as BaseHandleComponents;

class CustomHandleComponents extends BaseHandleComponents
{
    use ForwardsCalls;
    private BaseHandleComponents $baseHandleComponents;

    public function __construct(BaseHandleComponents $blueprint)
    {
        $this->baseHandleComponents = $blueprint;
    }

    protected function getView($component)
    {
        $oldName = $component->getName();
        $oldViewPath = config('livewire.view_path', resource_path('views/livewire'));
        debugbar()->debug("original: viewPath: $oldViewPath , dotName: $oldName");

        $dotName = $component->getName();
        if (str($dotName)->contains('album')) {
            $moduleName = str(get_class($component))->after('Modules\\')->before('\\')->toString();
            $viewPath = base_path("Modules/$moduleName/resources/views/livewire");
            $dotName = str($dotName)->after('::')->toString();

            $component->setName($dotName);
            config()->set('livewire.view_path', $viewPath);
        }

        $view = parent::getView($component);
        debugbar()->debug($view);

        $component->setName($oldName);
        config()->set('livewire.view_path', $oldViewPath);

        return $view;
    }

    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->baseHandleComponents, $method, $parameters);
    }
}
