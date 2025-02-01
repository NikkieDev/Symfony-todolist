<?php

namespace App\Objects;

enum ItemStatus: int
{
    case New = 0;
    case In_Progress = 1;
    case Completed = 2;
}
