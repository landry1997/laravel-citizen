<?php

namespace App\Actions;

use TCG\Voyager\Actions\AbstractAction;

class ExportPosition extends AbstractAction {

	public function getTitle() {
		return __("PDF");
	}

    public function getTooltype()
    {
        return __("Exporter le suivi des positions");
    }
	public function getIcon() {
		return 'voyager-download';
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
		return route('suivi.position2', array('suivi_id' => $this->data->code));
	}

	public function shouldActionDisplayOnDataType() {
		return $this->dataType->slug == 'demande-suivi';
	}

	// public function shouldActionDisplayOnRow($demande) {
	// 	return $demande->statut == 0;
	// }
}
