<?php
/** Выход с сайта */
logout();
flash('info', 'Вы вышли из учётной записи.');
redirect('/');
