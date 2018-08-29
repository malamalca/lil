<?php
    /*
        These are overrides for default FormHelper templates.
    */
    return [
        'inputSubmit' => '<input type="{{type}}" class="ui-button ui-widget ui-state-default ui-corner-all" {{attrs}}>',
        'inputContainer' => '<div class="input ui-widget ui-{{type}} {{type}}{{required}}">{{content}}</div>',
        'inputContainerError' => '<div class="input ui-widget ui-{{type}} {{type}}{{required}} error">{{content}}{{error}}</div>',
        'submitContainer' => '<div class="ui-widget ui-submit submit">{{content}}</div>'
    ];
