<?php

namespace Uspdev\Forms\Services;

use InvalidArgumentException;
use Uspdev\Forms\Form;
use Uspdev\Forms\Models\FormDefinition;
use Uspdev\Forms\Models\FormSubmission;

class FormRendererService
{
    public function render(FormDefinition $definition, array $options = [], ?FormSubmission $submission = null): string {
        
        $form = new Form(array_merge($options, [
            'name' => $definition->name,
            'version' => $definition->version,
        ]));

        $html = $form->generateHtmlFromDefinition($definition, $submission);

        if ($html === null) {
            throw new InvalidArgumentException("Form definition '{$definition->name}' nao encontrada.");
        }

        return $html;
    }
}
