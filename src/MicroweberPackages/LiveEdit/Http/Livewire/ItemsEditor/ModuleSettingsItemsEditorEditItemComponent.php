<?php

namespace MicroweberPackages\LiveEdit\Http\Livewire\ItemsEditor;

class ModuleSettingsItemsEditorEditItemComponent extends AbstractModuleSettingsEditorComponent
{
    public string $view = 'microweber-live-edit::module-items-editor-edit-item';
    public string $itemId = '';

    public array $itemState = [];


    public $listeners = [
        'onItemChanged' => '$refresh',
        'onReorderListItems' => 'reorderListItems',
    ];


    public function render()
    {
        if ($this->itemId) {
            $allItems = $this->getItems();
            if ($allItems) {
                foreach ($allItems as $item) {
                    if (isset($item['itemId']) and $item['itemId'] == $this->itemId) {
                        $this->itemState = $item;
                    }
                }
            }
        }

        return view($this->view);
    }


    public function submit()
    {
        $json = $this->getItems();
        $editorSettings = $this->getEditorSettings();

        $defaults = array(
            'itemId' => $this->moduleId . '_' . uniqid(),
        );

        if (isset($editorSettings['schema'])) {
            foreach ($editorSettings['schema'] as $field) {
                $fieldName = $field['name'];
                $defaultValue = isset($field['default']) ? $field['default'] : '';
                $defaults[$fieldName] = $defaultValue;
            }
        }

        if (isset($json) == false or count($json) == 0) {
            $json = array(0 => $defaults);
        }
        $isNewItem = false;

        $newItem = [];

        $newItemState = $this->itemState;

        if ($newItemState) {
            $newItem = $newItemState;
        }


        if ($this->itemId) {
            $newItem['itemId'] = $this->itemId;
        } else {
            $isNewItem = true;
            $newItem['itemId'] = $this->moduleId . '_' . uniqid();
        }

        $allItems = [];
        $allItems[] = $newItem;
        $sortIds = [];
        if (!empty($json)) {
            foreach ($json as $item) {
                if (isset($item['itemId']) and $newItem['itemId']) {
                    $sortIds[] = $item['itemId'];
                }
                if (isset($item['itemId']) and $newItem['itemId'] != $item['itemId']) {
                    $allItems[] = $item;
                }
            }
        }

        //sots $allItems by $sortIds
        if (!$isNewItem && $sortIds && $allItems) {
            array_multisort(array_column($allItems, 'itemId'), SORT_ASC, $allItems);
        }


        save_option(array(
            'option_group' => $this->moduleId,
            'module' => $this->moduleType,
            'option_key' => $this->getSettingsKey(),
            'option_value' => json_encode($allItems)
        ));

        $this->emitTo('microweber-live-edit::module-items-editor-list', 'onItemChanged', ['moduleId' => $this->moduleId]);

        $this->emit('switchToMainTab');


    }

    public function updatedSettings($settings)
    {
        $this->emit('settingsChanged', ['moduleId' => $this->moduleId, 'settings' => $this->settings]);
    }
}