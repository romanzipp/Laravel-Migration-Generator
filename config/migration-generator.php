<?php

return [
    /*
     * Where to store new generated migration files.
     */
    'output_path'        => database_path('migrations'),

    /*
     * How to name new created files.
     * Available placeholders:
     * - {date}   The current date & time in Y_m_d_His format
     * - {table}  The table name
     */
    'file_name_template' => '{date}_create_{table}_table.php',
];
