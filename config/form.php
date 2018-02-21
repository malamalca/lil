<?php
    return [
        'inputSubmit' => '<input type="{{type}}" class="ui-button ui-widget ui-state-default ui-corner-all" {{attrs}}>',
        'inputContainer' => '<div class="input ui-widget ui-{{type}} {{type}}{{required}}">{{content}}</div>',
        'inputContainerError' => '<div class="input ui-widget ui-{{type}} {{type}}{{required}} error">{{content}}{{error}}</div>',
        'submitContainer' => '<div class="ui-widget ui-submit submit">{{content}}</div>',

        'navbar-menu' => '<ul class="nav navbar-nav {{class}}">{{items}}</ul>',
        'navbar-item' => '<li><a href="{{url}}" class="btn {{class}}" {{attrs}}>{{content}}</a></li>',
        'navbar-submenu' => '<li><a href="#{{subid}}" class="popup_link" id="popup_{{subid}}" {{attrs}}>{{content}}<b class="carret"></b></a>' .
            '<div class="popup popup_{{subid}} ui-widget ui-widget-content ui-helper-clearfix ui-corner-all" style="display: none;">' .
            '<ul class="nav nav-sub">{{subitems}}</ul></div>' .
            '</li>',

        'linkedit' => sprintf('<a href="{{url}}" class="btn-edit {{class}}" role="button">%s</a>', __d('lil', 'edit')),
        'linkdelete' => sprintf('<a href="{{url}}" class="btn-delete {{class}}" role="button" onclick="return confirm(\'{{confirmation}}\');">%s</a>', __d('lil', 'delete')),

    ];
