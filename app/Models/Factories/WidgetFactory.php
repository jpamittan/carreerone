<?php

namespace Application\Models\Factories;

class WidgetFactory {
    /**
     * Widget types
     */
    private static $widgets = array(
        'pagination' => 'Application\Models\Widgets\PaginationWidget',
        'reminders' => 'Application\Models\Widgets\ReminderWidget',
        'similar-jobs' => 'Application\Models\Widgets\SimilarJobsWidget',
    );

    /**
     * Function to create a widget of passed in type with params
     * @param string $type
     * @param mixed $params optional
     * @return View
     */
    public static function create($type, $params = array()) {
        // check widgets for type
        $class = array_get(static::$widgets, $type, null);
        if (is_null($class)) {
            return '';
        }
        // create widget
        if (count($params) > 0) {
            $widget = new $class($params);
        } else {
            $widget = new $class;
        }
        return $widget->render();
    }
}
