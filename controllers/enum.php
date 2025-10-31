<?php

/**
 * User Roles
 */
enum Roles: string
{
    case USER = "USER";
    case STAFF = "STAFF";
    case ADMIN = "ADMIN";
}

enum SessionVariables: string
{
    case GENERIC_ERROR = 'error';
    case TRANSFER_ERROR = 'transfer';
    case USER_ID = 'UserID';
    case CUSTOMER_ID = 'CustomerID';
    case SUCCESS = 'success';
}

enum Routes: string
{
    case LOGIN_PAGE = '/login';
    case TRANSFER_PAGE = '/transfer';
}