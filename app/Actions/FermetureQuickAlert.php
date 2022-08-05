<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class FermetureQuickAlert extends AbstractAction {

	public function getTitle() {
		return __("Fermeture");
	}

	public function getIcon() {
		return 'voyager-x';
	}
    public function getTooltype()
    {
        return __("Fermer l'alerte rapide");
    }

	public function getPolicy() {
		return 'read';
	}

	public function getAttributes() {
		return [
			'class' => 'btn btn-sm btn-danger pull-right edit'
		];
	}

	public function getDefaultRoute() {
		return route('voyager.quick-alerte.edit', array('id' => $this->data->id));
	}

	public function shouldActionDisplayOnDataType() {
		return $this->dataType->slug == 'quick-alerte';
	}

	public function shouldActionDisplayOnRow($qalerte) {
		return $qalerte->statut == 0;
	}
}
