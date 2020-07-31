<?php

namespace Modules\Newsletter\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Newsletter\Entities\News;
use Symfony\Component\Workflow\Transition;

class CheckTransitionAvailableRule implements Rule {
    /**
     * CheckTransitionAvailableRule constructor.
     * @param $newsId
     * @param $target
     */
    public function __construct($newsId, $target) {
        $this->newsId = $newsId;
        $this->target = $target;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value) {
        $availableTransition = [];
        $model = $this->target::find($this->newsId);
        if ($model) {
            $availableTransition = array_map(function (Transition $t) {
                return $t->getName();
            }, $model->workflow_transitions());
        }
        return in_array($value, $availableTransition);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message() {
        return 'No Transition found from current state of object' ;
    }
}
