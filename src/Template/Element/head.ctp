<?php
    if (!isset($head_for_layout) || ($head_for_layout !== false)) {
        if (!empty($head_for_layout)) {
            if (is_string($head_for_layout)) {
                printf($this->element($head_for_layout));
            } else {
                printf($this->element($head_for_layout['element'], $head_for_layout['params']));
            }
        } else {
            printf('<div class="head"><h1>%s</h1></div>', $this->fetch('title'));
        }
    }