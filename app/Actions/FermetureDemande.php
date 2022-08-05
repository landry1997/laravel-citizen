<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class FermetureDemande extends AbstractAction {

	public function getTitle() {
		return __("Fermeture");
	}

	public function getIcon() {
		return 'voyager-x';
	}
    public function getTooltype()
    {
        return __("Fermer la demande de suivi");
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
		return route('voyager.demande-suivi.edit', array('id' => $this->data->id));
	}

	public function shouldActionDisplayOnDataType() {
		return $this->dataType->slug == 'demande-suivi';
	}

	public function shouldActionDisplayOnRow($demande) {
		return $demande->statut == 0;
	}
}
