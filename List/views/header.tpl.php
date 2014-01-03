<!DOCTYPE html>
<html>
    <head>
        <title>Todo List</title>
        <link rel="stylesheet" type="text/css" href="js/sorter-theme/style.css">
        <style type="text/css">
            body {
                font: 0.875em/1.1em 'Helvetica Neue', Helvetica, Arial, sans-serif;
                color: #333;
            }
            table {
                margin-bottom: 2em;
            }
            table td {
                padding: 0.3em 0.6em;
                border-bottom: 1px solid #ccc;
                line-height: 1.8em;
            }
            table tr:last-of-type td {
                border-bottom: 0;
            }
            table td p {
                margin: 0;
                line-height: 1.4em;
                padding: 4px 0;
            }
            table th {
                text-align: left;
                padding: 0.3em 0.6em;
                border-bottom: 3px solid #999;
                line-height: 1.8em;
            }
            .date {
                color: #999;
            }
            textarea {
                width: 99%;
                height: 100px;
                font: 1em/1.2em 'Helvetica Neue', Helvetica, Arial, sans-serif;
                resize: none;
            }
            input {
                font: 1.2em/1.2em 'Helvetica Neue', Helvetica, Arial, sans-serif;
                padding: 3px 6px;
            }
            .complete {
                text-decoration: line-through;
            }
            a {
                color: #39f;
            }
            a:hover {
                color: #06c;
            }
            code {
                font-size: 1.2em;
                background-color: #eee;
                padding: 2px;
            }
            ol, ul {
                margin: 0;
                padding: 0;
                padding-left: 24px;
            }
        </style>
    </head>
    <body>