$ = JQuery = require('jquery');
require('bootstrap');

Mustache = require('mustache');
numeral = require('numeral');
dt = require('datatables.net');
responsive = require('datatables.net-responsive');

require('./js/cordova');

Project = require('./js/project');
require('./js/main');
require('./js/users');

require('@fortawesome/fontawesome-free/scss/fontawesome.scss');
require('@fortawesome/fontawesome-free/scss/solid.scss');
require('bootstrap/scss/bootstrap.scss');
require('./scss/main.scss');