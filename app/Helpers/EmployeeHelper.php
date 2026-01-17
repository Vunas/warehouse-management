<?php

namespace App\Helpers;

class EmployeeHelper
{
    public static function getStatusBadge($isActive)
    {
        if ($isActive) {
            return '<span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Active</span>';
        }
        return '<span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Inactive</span>';
    }

    public static function formatPositionName($position)
    {
        return ucfirst(str_replace('_', ' ', $position));
    }
}