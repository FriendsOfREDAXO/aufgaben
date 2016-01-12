<?php

if (rex::isBackend()) {
    rex_view::addCssFile($this->getAssetsUrl('style.css'));
}
