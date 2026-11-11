<?php

namespace App\Support;

class ErrorHelper
{
    public static function friendly(\Throwable $e): string
    {
        if ($e instanceof \Illuminate\Database\QueryException) {
            $code = $e->errorInfo[1] ?? 0;
            $msg  = $e->getMessage();

            if ($code === 23505 || str_contains($msg, 'duplicate key') || str_contains($msg, 'UNIQUE constraint')) {
                return 'A record with these details already exists. Please use different information.';
            }
            if ($code === 23503 || str_contains($msg, 'foreign key')) {
                return 'This action cannot be completed because there are related records that depend on this one.';
            }
            if ($code === 23502 || str_contains($msg, 'not null')) {
                return 'Some required information is missing. Please fill in all required fields.';
            }
            if ($code === 23514 || str_contains($msg, 'check constraint')) {
                return 'The information provided is not valid for one of the fields. Please review and try again.';
            }
            if (str_contains($msg, 'Connection refused') || str_contains($msg, 'could not connect')) {
                return 'We are having trouble connecting to our system. Please try again in a moment.';
            }
            return 'We could not save your information. Please try again.';
        }

        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return collect($e->errors())->flatten()->first()
                ?? 'Please check your information and try again.';
        }

        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return 'The page or record you are looking for was not found.';
        }

        if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {
            return 'You do not have permission to perform this action.';
        }

        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return 'The record you are trying to access was not found.';
        }

        return 'Something went wrong. Please try again or contact support if the problem persists.';
    }
}