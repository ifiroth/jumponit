<?php

namespace JOI\Service;

class ActionManager {

    public function logAction($action, $id, $valueBefore, $valueAfter, $selection) {
        // TODO : log action into db

        return true;
    }

    public function getLoggedAction($action, $id) : array {
        // TODO : define getLoggedAction()

        return [];
    }
}
