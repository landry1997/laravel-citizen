<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ActiveUser extends AbstractAction {

	public function getTitle() {
		return __("Activer");
	}

    public function getTooltype()
    {
        return __("Activer cet utilisateur");
    }
	public function getIcon() {
		return 'voyager-check';
	}

	public function getPolicy() {
		return 'read';
	}

	public function getAttributes() {
		return [
			'class' => 'btn btn-sm btn-dark'
		];
	}

	public function getDefaultRoute() {
		return route('users.activeUser', array('id' => $this->data->id));
	}

	public function shouldActionDisplayOnDataType() {
		return $this->dataType->slug == 'users';
	}

	public function shouldActionDisplayOnRow($demande) {
		return $demande->statut == 0;
	}
}
