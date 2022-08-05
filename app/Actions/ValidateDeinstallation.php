<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ValidateDeinstallation extends AbstractAction {

	public function getTitle() {
		return __("Valider");
	}

    public function getTooltype()
    {
        return __("Valider la désinstallation");
    }
	public function getIcon() {
		return 'voyager-check';
	}

	public function getPolicy() {
		return 'read';
	}

    public function getAttributes()
    {
        return [
            'class' => 'btn btn-sm mx-auto btn-primary pull-right edit',
        ];
    }

	public function getDefaultRoute() {
		return route('manage.desinstallation', array('code' => $this->data->code, 'type'=>1));
	}

	public function shouldActionDisplayOnDataType() {
		return $this->dataType->slug == 'desinstallation';
	}

	public function shouldActionDisplayOnRow($demande) {
		return $demande->statut == 0;
	}
}
