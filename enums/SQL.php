<?php

namespace Enums;
enum SQL: string
{
    case IN_OPERATOR = 'IN';
    case IS_OPERATOR = 'IS';
    case NOT_IS_OPERATOR = 'IS NOT';
    case NOT_IN_OPERATOR = 'NOT IN';
    case NULL = 'NULL';

}