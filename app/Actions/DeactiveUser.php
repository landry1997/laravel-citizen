<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class DeactiveUser extends AbstractAction {

	public function getTitle() {
		return __("Désactiver");
	}

	public function getIcon() {
		return 'voyager-x';
	}

    public function getTooltype()
    {
        return __("Désactiver cet utilisateur");
    }

	public function getPolicy() {
		return 'read';
	}

	public function getAttributes() {
		return [
			'class' => 'btn btn-sm btn-danger'
		];
	}

	public function getDefaultRoute() {
		return route('users.deactiveUser', array('id' => $this->data->id));
	}

	public function shouldActionDisplayOnDataType() {
		return $this->dataType->slug == 'users';
	}

	public function shouldActionDisplayOnRow($demande) {
		return $demande->statut == 1;
	}
}
