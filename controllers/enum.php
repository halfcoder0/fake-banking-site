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
    case TRANSFER_ERROR = 'transfer_error';
    case USER_ID = 'UserID';
    case CUSTOMER_ID = 'CustomerID';
    case SUCCESS = 'success';
    case DEPOSIT_ERROR = 'deposit_error';
    case DEPOSIT_SUCCESS = 'deposit_success';
    case WITHDRAW_ERROR = 'withdraw_error';
    case WITHDRAW_SUCCESS = 'withraw_success';
    case UPDATE_STAFF_STATUS = 'update_staff_status';
    case CREATE_STAFF_STATUS = 'create_staff_status';
    case NONCE = 'nonce';
}

enum Routes: string
{
    case LOGIN_PAGE = '/login';
    case TRANSFER_PAGE = '/transfer';
}
