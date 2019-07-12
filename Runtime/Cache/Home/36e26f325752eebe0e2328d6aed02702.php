<?php if (!defined('THINK_PATH')) exit();?>
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>专业版K线</title>
<script src="/Public/Home/js/movesay.js"></script>
<script src="/Public/Home/js/highstock.js"></script>
<style type="text/css">
body {
 overflow: hidden; margin: 0px; font-size: 14px; font-family: Arial, sans; line-height: 18px;
}

a {
 color: #0D86FF; text-decoration: none; cursor: pointer;
}

html,body {
 height: 100%;
}

.green {
 color: green;
}

.red {
 color: red;
}

.ab {
 position: absolute; left: 0px; top: 0px;
}

ul.horiz {
 list-style: none; padding: 0px; margin: 0px; padding-left: 6px;
}

ul.horiz>li {
 display: inline-block; *display: inline; *zoom: 1; vertical-align: baseline; margin-right: 12px;
}

.price {
 margin-right: 60px;
}

ul.horiz>li.sep {
 height: 16px; vertical-align: middle;
}

ul.horiz li.subsep {
 height: 16px; vertical-align: middle; margin-right: 6px;
}

ul.horiz li.addthis {
 position: absolute; right: 0px; margin-top: 6px; vertical-align: top; width: 190px;
}

ul.horiz li.last {
 margin-right: 0px;
}

ul.horiz input,ul.horiz select,ul.horiz button {
 vertical-align: middle;
}

#nav {
 left: 0px; top: 0px; border-bottom: 1px solid #333; font-size: 12px;
}

#nav .logo {
 line-height: 32px; font-size: 14px; font-weight: bold; font-family: Arial, sans; margin-left: 0px;
}

#nav .logo a {
 margin: 0px;
}

#nav .horiz {
 height: 32 px;
}

#control {
 font-size: 12px; font-weight: bold; border-bottom: 1px solid #333;
}

#control select,#control input,#control button {
 font-size: 12px; font-family: serif; outline: none;
}

#sidebar_outer {
 float: right; font-family: Consolas, monospace; height: 100%; font-size: 14px; line-height: 13px;
}

#sidebar {
 height: 100%; width: 220px; border-left: 1px solid #333; padding-left: 6px;
}

#sidebar #market {
 font-size: 14px; font-weight: bold; text-align: center; height: 18px; line-height: 18px; font-family: Arial, sans; padding: 6px;
}

#sidebar #price {
 text-align: center; font-size: 16px; font-weight: bold; height: 28px; line-height: 28px;
}

#sidebar #trades {
 overflow-y: auto; text-align: left;
}

#sidebar #trades span {
 font-size: 1.6em; font-weight: bold;
}

#sidebar #trades .row {
 display: block;
}

#middlebar {
 width: 120px; height: 100%; padding: 6px; border-right: 1px solid #333; text-align: center; float: right;
}

#main {
 font-family: Consolas, monospace; font-size: 12px; line-height: 14px; overflow: hidden;
}

#main #wrapper {
 height: 100%; position: relative; overflow: hidden;
}

#main .hide_cursor canvas,#main .hide_cursor #chart_info {
 cursor: none;
}

#footer {
 border-top: 1px solid #333; line-height: 32px;
}

#assist {
 overflow-y: auto; position: absolute; right: 0px; bottom: 0px; width: 320px; height: 32px; padding: 0px 4px; border-left: 1px solid #333; border-top: 1px solid #333; font-size: 11px; line-height: 16px; color: #333; background-color: rgba(255, 255, 255, 0.8);
}

#info {
 position: absolute; font-size: 10px; font-family: Consolas, monospace; color: #999999;
}

#leftbar_outer {
 float: right; height: 100%;
}

#leftbar_outer #leftbar {
 border-left: 1px solid #333;
}

#leftbar_outer .gg160x600 {
 width: 160px; padding: 6px;
}

#close_ad {
 text-align: center; padding: 2em;
}

#leftbar {
 height: 100%; font-size: 12px; position: relative; font-family: Consolas, monospace; border-right: 1px solid #ccc;
}

#date {
 position: absolute; bottom: 0px; width: 100%; height: 16px; line-height: 16px; text-align: center; border-top: 1px solid #ccc;
}

#periods li.period {
 cursor: pointer; color: #0D86FF;
}

#periods li.selected {
 color: #F80;
}

.gg468x60 {
 position: absolute; right: 0px; top: 0px; z-index: 100; width: 468px; height: 60px; border-left: 1px solid #ccc; border-bottom: 1px solid #ccc;
}

.gg200x200 {
 height: 200px; overflow: hidden; margin-top: 6px; border-top: 1px solid #333; padding-top: 6px;
}

#asks,#bids {
 width: 300px;
}

#gasks,#gbids {
 width: 80px;
}

.new {
 background-color: #333;
}

#orderbook {
 line-height: 0px;
}

#orderbook .orderbook {
 overflow: hidden;
}

#orderbook #asks,#orderbook #gasks,#orderbook #bids,#orderbook #gbids {
 height: 195px; position: relative; display: inline-block; *display: inline; *zoom: 1; overflow: hidden;
}

#orderbook .table {
 position: absolute; border-collapse: collapse; padding: 0px; margin: 0px;
}

#orderbook .table .row {
 padding: 0px; margin: 0px; height: 16px; line-height: 16px;
}

#orderbook .remove {
 color: #333;
}

#orderbook .remove g {
 color: #333;
}

#orderbook #asks .table,#orderbook #gasks .table {
 bottom: 0px;
}

#orderbook #bids .table,#orderbook #gbids .table {
 top: 0px;
}

#before_trades {
 padding-bottom: 6px; border-bottom-width: 1px; margin-bottom: 6px;
}

g {
 color: #CCC;
}

h {
 color: white;
}

.donate {
 text-align: center; font-size: 12px;
}

.markets_outer {
 position: absolute; top: 0px; left: 0px; width: 100%; text-align: center;
}

#market_status {
 font-size: 11px;
}

#market_status label {
 font-weight: normal; margin: 0px 6px;
}

abbr {
 cursor: help; font-weight: normal; border-bottom: 1px dotted #333;
}

#qr {
 border: 1px solid black; left: 0px; top: 0px; padding: 8px; background-color: white; position: absolute; z-index: 105; display: none;
}

.donate {
 font-family: Consolas, monospace;
}

#settings {
 width: 60%; left: 20%; top: 20%; position: absolute; background-color: rgba(255, 255, 255, 0.6); border-radius: 5px; box-shadow: 0px 0px 3px #666; padding: 12px; display: none;
}

#settings input {
 width: 2em; vertical-align: top;
}

#settings h3 {
 border-bottom: 1px solid #333;
}

#settings dl dt,#settings dl dd {
 display: inline-block; *display: inline; *zoom: 1; margin: 0px; padding: 0px; margin-right: 6px;
}

#settings #close_settings {
 text-align: center; margin: 12px;
}

button,select {
 cursor: pointer;
}

#main,#footer {
 display: none;
}

#loading,.notify {
 position: absolute; top: 20%; width: 100%; font-size: 18px; text-align: center; z-index: 99;
}

#loading .inner,.notify .inner {
 margin-top: -32px; line-height: 32px; display: inline-block; *display: inline; *zoom: 1; padding: 12px 24px; border: 1px solid #333; border-radius: 5px; background-color: rgba(10, 10, 10, 0.8);
}

.dark {
 background-color: #0A0A0A; color: #CCC;
}

.dark #trades {
 scrollbar-basecolor: #333; scrollbar-highlight-color: #CCC; scrollbar-shadow-color: #666;
}

.dark :-webkit-scrollbar {
 background-color: #333;
}

.dark g {
 /*color: #666;*/
 
}

.dark h {
 visibility: hidden;
}

.dark a {
 color: #6BF;
}

.dark a:hover {
 text-decoration: underline;
}

.dark #close_ad span {
 cursor: pointer; color: #6BF;
}

.dark .green {
 color: #0C0;
}

.dark .red {
 color: #C00;
}

.dark a.active {
 color: #FC9;
}

.dark a.active.grey {
 color: #CCA37A;
}

.dark #periods li.period {
 color: #6BF;
}

.dark #periods li.selected a {
 color: #FB6;
}

.dark a.selected {
 color: #FB6;
}

.dark #sidebar .green {
 color: #6C6;
}

.dark #sidebar .red {
 color: #C66;
}

.dark #sidebar #price.green {
 color: #0F0;
}

.dark #sidebar #price.red {
 color: #F00;
}

.dark #assist {
 background-color: rgba(10, 10, 10, 0.8); font-weight: normal; color: #999;
}

.dark #nav,.dark #control,.dark #before_trades {
 border-bottom: 1px solid #333;
}

.dark #leftbar {
 border-right: 1px solid #333;
}

.dark #date {
 border-top: 1px solid #333;
}

.dark select,.dark button,.dark option {
 background-color: #333; color: #CCC; border: 1px solid #999;
}

.dark .gg468x60 {
 background-color: #0A0A0A; border-left: 1px solid #333; border-bottom: 1px solid #333;
}

.dark .markets_outer .markets {
 background-color: #222; border: 1px solid #666;
}

.dark li.sep,.dark li.subsep {
 border-right: 1px solid #333;
}

.dark #qr {
 border: 1px solid #333; background-color: rgba(10, 10, 10, 0.8);
}

.dark #settings {
 background-color: rgba(10, 10, 10, 0.6);
}

.dark abbr {
 border-bottom: 1px dotted #CCC;
}

.dark .external .green {
 color: #6C6;
}

.dark .external .red {
 color: #C66;
}

.dark ::-webkit-scrollbar {
 width: 16px; height: 13px;
}

.dark ::-webkit-scrollbar-button:start:decrement,.dark ::-webkit-scrollbar-button:end:increment {
 height: 0px; width: 0px;
}

.dark ::-webkit-scrollbar-track-piece {
 background-color: #222; border: 1px solid #555;
}

.dark ::-webkit-scrollbar-thumb:vertical {
 height: 50px; background: -webkit-gradient(linear, left top, right top, color-stop(0%, #4d4d4d), color-stop(100%, #333333)); border: 1px solid #0d0d0d; border-top: 1px solid #666666; border-left: 1px solid #666666;
}

.dark ::-webkit-scrollbar-thumb:horizontal {
 width: 50px; background: -webkit-gradient(linear, left top, left bottom, color-stop(0%, #4d4d4d), color-stop(100%, #333333)); border: 1px solid #1f1f1f; border-top: 1px solid #666666; border-left: 1px solid #666666;
}

.dark .help {
 background-color: #0A0A0A; border: 1px solid #333;
}

.dark .grey {
 color: grey;
}

.dark .ticker_red {
 background-color: #F66; color: #0A0A0A;
}

.dark .ticker_green {
 background-color: #6C6; color: #0A0A0A;
}

.light {
 background-color: #FFF; color: #333;
}

.light g {
 color: #AAA;
}

.light h {
 visibility: hidden;
}

.light a {
 color: #0D86FF;
}

.light a:hover {
 text-decoration: underline;
}

.light #close_ad span {
 color: #0D86FF; cursor: pointer;
}

.light .green {
 color: green;
}

.light .red {
 color: red;
}

.light #periods li.period {
 color: #0D86FF;
}

.light #periods li.selected a {
 color: #F80;
}

.light a.selected {
 color: #F80;
}

.light .active {
 color: #F80;
}

.light a.active.grey {
 color: #CC6D00;
}

.light #assist {
 background-color: rgba(255, 255, 255, 0.8); font-weight: normal; color: #333;
}

.light #nav,.light #control,.light #before_trades {
 border-bottom: 1px solid #CCC;
}

.light #sidebar,.light #footer,.light #assist {
 border-color: #CCC;
}

.light #leftbar_outer #leftbar {
 border-left: 1px solid #CCC;
}

.light #date {
 border-top: 1px solid #CCC;
}

.light ul.horiz select,.light ul.horiz button,.light ul.horiz option {
 background-color: #FFF; color: #333; border: 1px solid #666;
}

.light .gg468x60 {
 background-color: #FFFFFF; border-left: 1px solid #CCC; border-bottom: 1px solid #CCC;
}

.light .gg200x200 {
 border-color: #CCC;
}

.light li.sep,.light li.subsep {
 border-right: 1px solid #DDD;
}

.light #qr {
 border: 1px solid #ccc; background-color: rgba(255, 255, 255, 0.8);
}

.light #loading .inner {
 background-color: rgba(255, 255, 255, 0.8); border: 1px solid #CCC;
}

.light .dropdown .t {
 border: 1px solid #FFF;
}

.light .dropdown-data {
 background-color: rgba(255, 255, 255, 0.9); border: 1px solid #999;
}

.light .dropdown-hover .t {
 color: #666; border: 1px solid #999; border-bottom: none; background-color: rgba(255, 255, 255, 0.9);
}

.light #nav-charts td,.light table.simple td {
 border-bottom: 1px solid #CCC;
}

.light .navbar {
 border-bottom: 1px solid #CCC;
}

.light table.simple ul li {
 color: #0D86FF;
}

.light table.simple ul li.active {
 color: #F80;
}

.light #trades .v {
 color: #000;
}

.light #now {
 color: #333;
}

.light .difficulty table {
 background-color: white; border: 1px solid #CCC;
}

.light .new {
 background-color: #CCC;
}

.light #orderbook .remove {
 color: #CCC;
}

.light #orderbook .remove g {
 color: #CCC;
}

.light .help {
 background-color: #FFF; border: 1px solid #CCC;
}

.light .grey {
 color: grey;
}

.light .ticker_green {
 background-color: green; color: white;
}

.light .ticker_red {
 background-color: red; color: white;
}

.light .dialog {
 background-color: rgba(255, 255, 255, 1); color: #333; border: 1px solid #ccc;
}

.light .dialog .tablist {
 border-bottom: 1px solid #CCCCCC; background-color: #FFFFFF;
}

.light .dialog .tablist .selected {
 color: #F80;
}

.light .dialog .content .selected {
 background-color: #ccc;
}

.light input {
 color: #333333; background-color: #FFFFFF; border: 1px solid #CCCCCC;
}

.light input[type=button]:hover,.light input[type=submit]:hover {
 background-color: #E6E6E6;
}

.light input[type=button]:active,.light input[type=submit]:active {
 background-color: #F2F2F2;
}

.light #mode a {
 color: #666;
}

.light #mode a:hover,.light #mode a.selected {
 border: 1px solid #eee;
}

.light #mode a.selected {
 background-color: #eee; color: #000;
}

#donation {
 color: #333; font-size: 10px; font-family: Monaco, Consolas, Monospace; vertical-align: top;
}

.dark .COINWIDGETCOM_WINDOW input {
 color: #333; background-color: #FFF;
}

.light .COINWIDGETCOM_WINDOW input {
 color: #333; background-color: #FFF;
}

.COINWIDGETCOM_WINDOW input {
 font-size: 11px !important;
}

.COINWIDGETCOM_CONTAINER>span {
 color: #333 !important;
}

.COINWIDGETCOM_CONTAINER>a {
 height: 16px !important;
}

.COINWIDGETCOM_CONTAINER>span {
 line-height: 16px !important; padding: 0px 2px;
}

#mtgoxNav div {
 display: block;
}

.nowrap {
 white-space: nowrap;
}

.nav-markets {
 font-family: Consolas, monospace;
}

.nav-markets a {
 margin-right: 6px;
}

#markets {
 right: 0px; font-size: 11px; text-align: center; font-weight: normal;
}

#markets a {
 margin: 0px 3px;
}

#markets .currency {
 font-size: 12px;
}

#markets div {
 display: inline-block; *display: inline; *zoom: 1; font-family: Consolas, monospace; margin-right: 12px; margin-left: 0px; text-align: left;
}

.dropdown,.dropup {
 padding: 0px; margin: 0px; display: inline-block; *display: inline; *zoom: 1; list-style: none; position: relative; z-index: 10;
}

.dropdown .t,.dropup .t {
 border: 1px solid #0A0A0A; border-bottom: none; padding: 3px 6px; z-index: 101; position: relative; cursor: default;
}

.dropdown .caret,.dropup .caret {
 margin-left: 6px;
}

.dropdown .caret .icon-caret-down,.dropup .caret .icon-caret-down {
 display: inline;
}

.dropdown .caret .icon-caret-up,.dropup .caret .icon-caret-up {
 display: none;
}

.dropdown-hover,.dropup-hover {
 z-index: 100;
}

.dropdown-hover .dropdown-data,.dropup-hover .dropdown-data {
 display: block;
}

.dropdown-hover .t,.dropup-hover .t {
 color: #FFF; border: 1px solid #666; border-bottom: none; background-color: rgba(10, 10, 10, 0.9);
}

.dropdown-hover .caret .icon-caret-down,.dropup-hover .caret .icon-caret-down {
 display: none;
}

.dropdown-hover .caret .icon-caret-up,.dropup-hover .caret .icon-caret-up {
 display: inline;
}

.dropdown-data,.dropup-data {
 display: none; position: absolute; z-index: 100; background-color: rgba(10, 10, 10, 0.9); padding: 6px; margin: 0px; text-align: left; border: 1px solid #666; margin-top: -1px;
}

.dropdown-data li,.dropup-data li {
 display: block; white-space: nowrap;
}

.navbar {
 font-size: 11px; border-bottom: 1px solid #333;
}

.navbar .nav {
 padding: 0px; margin: 0px; list-style: none; padding-left: 3px;
}

.navbar .nav li {
 display: inline-block; *display: inline; *zoom: 1; margin-right: 18px;
}

.navbar .nav li.logo {
 margin-left: 3px; font-size: 14px; line-height: 32px;
}

.navbar .nav li.ticker span {
 margin-left: 6px; font-family: Consolas, monospace;
}

.navbar .nav li.ticker span.eprice {
 margin-left: 0px;
}

#nav-charts {
 font-weight: normal;
}

table.simple {
 font-weight: normal; border-collapse: collapse; font-size: 12px;
}

table.simple td {
 padding: 6px; border-bottom: 1px solid #333;
}

table.simple tr:last-child td {
 border-bottom: none;
}

table.simple ul {
 list-style: none; padding: 0px; margin: 0px;
}

table.simple ul li {
 display: inline-block; *display: inline; *zoom: 1; margin-right: 12px; color: #6BF; cursor: pointer;
}

table.simple ul li:hover {
 text-decoration: underline;
}

table.simple ul li.active {
 color: #FC9;
}

table.simple {
 border-collapse: collapse;
}

.ggtop {
 position: absolute; top: 0px; right: 0px;
}

.ggtop img {
 display: block;
}

#trades {
 color: #808080;
}

#trades .row {
 clear: both; height: 16px; line-height: 16px;
}

#trades .t,#trades .p,#trades .v {
 display: inline-block; *display: inline; *zoom: 1; margin-right: 10px; vertical-align: top;
}

#trades .v {
 color: #CCC;
}

#trades .p {
 overflow: hidden;
}

#trades .v {
 float: right; text-align: left;
 /*width: 5em;*/
}

#trades .s {
 text-align: right;
}

#now {
 position: absolute; right: 0px; color: #999; padding-right: 6px;
}

.watch_list {
 background-color: black;
}

.watch_list table td {
 border: 1px solid #333; padding: 6px;
}

.notify {
 display: none;
}

.difficulty {
 position: absolute; right: 0px; top: 5px; width: 220px; font-family: Arial, sans; font-size: 11px; text-align: right;
}

.difficulty table {
 margin: auto; border: 1px solid #333; border-collapse: collapse; text-align: left; background-color: #0A0A0A;
}

.difficulty table td {
 overflow: hidden; height: 16px; padding: 0px 4px; line-height: 16px;
}

.difficulty span {
 font-family: Consolas, monospace;
}

@media ( max-width : 1200px) {
 .difficulty {
  display: none;
 }
}

@media ( max-width : 1024px) {
 .eprice {
  display: none;
 }
}

.more {
 text-align: right;
}

.navbar .nav li.passport {
 font-size: 13px; float: right; line-height: 32px; height: 32px; margin: 0px; padding: 0px;
}

.navbar .nav li.passport>div {
 margin-right: 8px; display: inline-block; *display: inline; *zoom: 1;
}

.address {
 font-family: Consolas, monospace;
}

a.direct_address {
 color: #6C6;
}

#footer .conn {
 color: #090;
}

.good {
 color: #090;
}

.normal {
 color: #990;
}

.bad {
 color: #900;
}

.unselectable {
 -moz-user-select: -moz-none; -khtml-user-select: none; -webkit-user-select: none; -ms-user-select: none; user-select: none;
}

input {
 padding: 4px; border-radius: 4px; border: 1px solid #333333; background-color: #333333; color: #CCCCCC;
}

input[type=text],input[type=password] {
 width: 12em;
}

input[type=checkbox] {
 vertical-align: middle;
}

input[type=button],input[type=submit] {
 padding: 4px 8px; cursor: pointer;
}

input[type=button]:hover,input[type=submit]:hover {
 background-color: #474747;
}

input[type=button]:active,input[type=submit]:active {
 background-color: #333333;
}

.dialog {
 position: absolute; left: 50%; top: 40%; width: 640px; padding: 8px; border: 1px solid #333; font-family: Arial, sans; font-size: 13px; background-color: #1A1A1A; color: #CCCCCC; z-index: 101; display: none;
}

.dialog .tablist {
 border-bottom: 1px solid #333333; background-color: #0A0A0A; margin-bottom: 8px; cursor: move;
}

.dialog .tablist .tab {
 display: inline-block; *display: inline; *zoom: 1; padding: 8px; font-size: 12px; width: 64px; text-align: center; margin-right: 6px; cursor: pointer;
}

.dialog .tablist .selected {
 color: #FC6;
}

.dialog .tablist .tab:hover {
 text-decoration: underline;
}

.dialog .tablist .close {
 width: auto; padding: 8px; text-align: right; float: right;
}

.dialog .tablist i.icon-ok {
 color: #6C6;
}

.dialog .tablist table {
 border-collapse: collapse;
}

.dialog .tablist table td {
 padding: 2px 4px;
}

.dialog .tablist table td:first-child {
 text-align: right; width: 160px;
}

.dialog .tablist-one .tab {
 width: auto;
}

.dialog-auto-size {
 width: auto; height: auto;
}

.dialog-auto-size form table td:first-child {
 width: auto;
}

.dialog table.s1 {
 width: 100%; text-align: center; border-collapse: collapse; margin-bottom: 8px;
}

.dialog table.s1 i.icon-ok {
 color: #6C6;
}

.dialog table.s1 i.icon-remove {
 color: #F66;
}

.dialog table.s1 td,.dialog table.s1 th {
 border: 1px solid #4D4D4D; padding: 4px;
}

.dialog h3 {
 margin-top: 0px; margin-bottom: 8px; padding-top: 4px; font-size: 12px;
}

.dialog dl {
 margin-top: 0px;
}

.dialog ol {
 margin: 0px; padding: 0px;
}

.dialog ol li {
 margin-left: 40px;
}

.del {
 text-decoration: line-through;
}

.del i {
 text-decoration: none;
}

.ic {
 font-family: FontAwesome; font-style: normal;
}

.dialog .error {
 color: #F33; text-align: center; display: none;
}

.dialog .ok {
 color: #6C6; text-align: center;
}

.dialog .content_premium table#price_table {
 width: 300px; margin: auto; margin-bottom: 8px;
}

.dialog .content_premium .steps {
 text-align: center;
}

#dlg_ucp {
 width: 420px;
}

#dlg_ucp .content {
 height: 250px; overflow-y: auto;
}

#dlg_ucp .content_home {
 text-align: center; font-size: 13px; line-height: 20px;
}

#dlg_ucp .content_home dl {
 text-align: left; display: inline-block; *display: inline; *zoom: 1;
}

#dlg_ucp .content_home dl dd {
 margin-bottom: 6px;
}

#dlg_ucp .content_history {
 text-align: left; overflow-y: auto;
}

#dlg_ucp .content_history table {
 font-size: 11px;
}

.dialog .content .selected {
 background-color: #333; border-radius: 3px;
}

.dialog .unit {
 padding: 3px; cursor: pointer;
}

.dialog-toolbox {
 padding: 0px;
}

.dialog-toolbox .tablist {
 background-color: #333333; padding: 0px 8px;
}

.dialog-toolbox .tablist .tab {
 padding: 0px; font-size: 10px; margin: 0px; width: auto;
}

.dialog-toolbox .tablist .close {
 padding: 0px; font-size: 12px;
}

.dialog-toolbox .content {
 margin: 8px;
}

.content_warning {
 text-align: center;
}

.content_warning .icon {
 float: left; font-size: 36px;
}

.help {
 position: absolute; left: 0px; top: 0px; background-color: #0A0A0A; font-size: 11px; padding: 8px; border: 1px solid #333; display: none;
}

.help dl:first-child {
 margin-top: 0px;
}

.bw-circle {
 width: 10px; height: 10px; display: inline-block; *display: inline; *zoom: 1; background-color: #090; vertical-align: baseline; border-radius: 3px;
}

.good .bw-circle {
 background-color: #090;
}

.normal .bw-circle {
 background-color: #990;
}

.bad .bw-circle {
 background-color: #900;
}

#connection .conn {
 font-size: 0px;
}

.reason {
 text-align: left; font-weight: normal; font-size: 14px;
}

.reason dt {
 font-weight: bold;
}

#chart_info {
 position: absolute; top: 0px; left: 0px; font-family: Consolas, monospace; font-size: 12px; padding: 4px; width: 100%; text-align: left;
}

#chart_info .yellow {
 color: #EE0;
}

.light #chart_info .yellow {
 color: #03E;
}

#mode {
 font-family: Consolas, monospace; font-size: 12px; height: 25px; line-height: 25px;
}

#mode a {
 display: inline-block; *display: inline; *zoom: 1; width: 17px; height: 17px; line-height: 17px; text-align: center; text-decoration: none; color: #999; font-weight: normal; vertical-align: middle; border-radius: 3px; border: 1px solid transparent;
}

#mode a:hover,#mode a.selected {
 border: 1px solid #333; color: #ccc;
}

#mode a.selected {
 color: #ccc; border: 1px solid #333;
}

#mode img {
 vertical-align: middle;
}

.hint {
 font-size: 11px; font-family: Arial, sans;
}

code {
 font-family: Consolas, monospace;
}

.warning {
 color: #FF0; font-size: 14px; font-family: Arial, sans; padding: 4px 8px; font-weight: bold;
}

.light .warning {
 color: #F00;
}

.hide {
 display: none;
}
</style>
</head>
<body>
	<div id="header_outer">
		<div id="header">
			<div class="navbar navbar-static-top">
				<div class="navbar-inner">
					<div class="container">
						<div id="warning"></div>
					</div>
				</div>
			</div>
			<div id="qr">
				<img />
			</div>
			<div id="control">
				<div class="inner">
					<ul id="periods" class="horiz">
						<li style='line-height: 26px'>K线时间段:</li>
						<li class="period"><a>1周</a></li>
						<li class="subsep"></li>
						<li class="period"><a>1天</a></li>
						<li class="subsep"></li>
						<li class="period"><a>12小时</a></li>
						<li class="period"><a>6小时</a></li>
						<li class="period"><a>4小时</a></li>
						<li class="period"><a>2小时</a></li>
						<li class="period"><a>1小时</a></li>
						<li class="subsep"></li>
						<li class="period"><a>30分</a></li>
						<li class="period selected"><a>15分</a></li>
						<li class="period"><a>5分</a></li>
						<li class="period"><a>3分</a></li>
						<li class="period"><a>1分</a></li>
						<li class="subsep"></li>
						<li>
							<div class="dropdown">
								<div class="t">页面设置</div>
								<div class="dropdown-data">
									<table class="nowrap simple settings">
										<tbody>
											<tr class="main_lines">
												<td>均线设置</td>
												<td><ul id="setting_main_lines">
														<li value="mas" class="active">MA</li>
														<li value="emas">EMA</li>
														<li value="none">关闭均线</li>
													</ul></td>
											</tr>
											<tr class="stick_style">
												<td>图线样式</td>
												<td><ul id="setting_stick_style">
														<li value="candle_stick" class="active">K线-OHLC</li>
														<li value="candle_stick_hlc">K线-HLC</li>
														<li value="ohlc">OHLC</li>
														<li value="line">单线</li>
														<li value="line_o">单线-o</li>
														<li value="none">关闭线图</li>
													</ul></td>
											</tr>
											<tr class="line_style" style="display: none;">
												<td>Line Style</td>
												<td><ul id="setting_ls">
														<li value="c" class="active">Close</li>
														<li value="m">Median Price</li>
													</ul></td>
											</tr>
											<tr class="indicator">
												<td>技术指标</td>
												<td><ul id="setting_indicator">
														<li value="macd">MACD</li>
														<li value="kdj">KDJ</li>
														<li value="stoch_rsi" class="active">StochRSI</li>
														<li value="none">关闭指标</li>
													</ul></td>
											</tr>
											<tr class="scale">
												<td>线图比例</td>
												<td><ul id="setting_scale">
														<li value="normal">普通K线</li>
														<li value="logarithmic" class="active">对数K线</li>
													</ul></td>
											</tr>
											<tr>
												<td></td>
												<td colspan="2"><a id="btn_settings">指标参数设置</a></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
							<div class="dropdown">
								<div class="t">工具</div>
								<div class="dropdown-data">
									<table class="nowrap simple">
										<tbody>
											<tr>
												<td><a class="mode" mode="draw_line">画线</a><br> <a class="mode" mode="draw_fhline">绘制斐波那契回调线</a> <br> <a class="mode" mode="draw_ffan">绘制斐波那契面</a><br> <a class="mode selected" mode="draw_fhlineex">绘制斐波那契扩展</a>
													<div class="hint">
														点击左键画点/线 <br>点击右键清除
													</div></td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</li>
						<li class="sep"></li>
						<li id="mode">
							<a id="mode_cross" class="mode" title="Cross Cursor" mode="cross">
								<img src="/Public/Home/images/shape-cross.png">
							</a>
							<a id="mode_draw_line" class="mode" title="Draw lines" mode="draw_line">
								<img src="/Public/Home/images/shape-line.png">
							</a>
							<a id="mode_draw_fhline" class="mode" title="Draw Fibonacci Retracements" mode="draw_fhline">
								<img src="/Public/Home/images/shape-fr.png">
							</a>
							<a id="mode_draw_ffan" class="mode" title="Draw Fibonacci Fans" mode="draw_ffan">
								<img src="/Public/Home/images/shape-ffan.png">
							</a>
						</li>
						<li class="sep"></li>
						<li>
							已更新
							<span id="change">12</span>
							秒
							<span id="realtime_error" style="display: none">
								in <abbr title="Realtime(WebSocket) connection failed. Orderbook update every 1 minute, Trades update every 10 seconds.">Slow Mode</abbr>
							</span>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div id="loading">
		<div class="inner">
			<div class="text">Loading...</div>
		</div>
	</div>
	<div id="notify" class="notify">
		<div class="inner"></div>
	</div>
	<div id="main">
		<div id="sidebar_outer" style="display: none">
			<div id="sidebar" style="display: none">
				<div id="trades"></div>
			</div>
		</div>
		<div id="wrapper" class="hide_cursor">
			<canvas id="canvas_main"></canvas>
			<canvas id="canvas_shapes" class="ab"></canvas>
			<canvas id="canvas_cross" class="ab"></canvas>
			<div id="chart_info"></div>
		</div>
	</div>
	<div id="footer_outer">
		<div id="footer">
			<!-- <ul class="horiz donate"><li>
</li><li id="now"></li></ul> -->
		</div>
	</div>
	<div id="assist" style='display: none'></div>
	<div id="settings">
		<h2>技术指标参数设定</h2>
		<table>
			<tr id="indicator_price_mas">
				<th>EMA / MA <abbr title="Up to 4 different indicators
Set field blank to remove one of the indicators.">?</abbr>
				</th>
				<td><input name=price_mas /> <input name=price_mas /> <input name=price_mas /> <input name=price_mas /></td>
				<td><button>默认值</button></td>
			</tr>
			<tr id="indicator_macd">
				<th>MACD <abbr title="Short, Long, Move">?</abbr>
				</th>
				<td><input name=macd /> <input name=macd /> <input name=macd /></td>
				<td><button>默认值</button></td>
			</tr>
			<tr id="indicator_kdj">
				<th>KDJ <abbr title="rsv, k, d">?</abbr>
				</th>
				<td><input name=kdj /> <input name=kdj /> <input name=kdj /></td>
				<td><button>默认值</button></td>
			</tr>
			<tr id="indicator_stoch_rsi">
				<th>Stoch RSI <abbr title="Params: Stochastic Length, RSI Length, K, D">?</abbr>
				</th>
				<td><input name=stoch_rsi /> <input name=stoch_rsi /> <input name=stoch_rsi /> <input name=stoch_rsi /></td>
				<td><button>默认值</button></td>
			</tr>
		</table>
		<div id="close_settings">
			<a>[ 关闭 ]</a>
		</div>
	</div>
	<script type=text/javascript>
		(function(){
			window.$market='<?php echo ($market); ?>';
			window.$sid="00";
			window.$time_fix=60*1000;
			window.$host='';
			window.$test=false;
			window.$symbol="1";
			window.$hsymbol="RUIZCON XNB\/CNY";
			window.$theme_name="white";
			window.$debug=false;
			window.$settings={
                "main_lines":{
                    "id":"main_lines",
                    "name":"Main Indicator",
                    "default":"mas",
                    "options":{
                        "MA":"mas",
                        "EMA":"emas",
                        "None":"none"
                    }
                },
                "stick_style":{
                    "id":"stick_style",
                    "name":"Chart Style",
                    "options":{
                        "CandleStick":"candle_stick",
                        "CandleStickHLC":"candle_stick_hlc",
                        "OHLC":"ohlc",
                        "Line":"line",
                        "Line-o":"line_o",
                        "None":"none"
                    }
                },
                "line_style":{
                    "id":"ls",
                    "name":"Line Style",
                    "options":{
                        "Close":"c",
                        "Median Price":"m"
                    }
                },
                "indicator":{
                    "id":"indicator",
                    "name":"Indicator",
                    "default":"none",
                    "options":{
                        "MACD":"macd",
                        "KDJ":"kdj",
                        "StochRSI":"stoch_rsi",
                        "None":"none"
                    }
                },
                "scale":{
                    "id":"scale",
                    "name":"Scale",
                    "options":{
                        "Normal":"normal",
                        "Logarithmic":"logarithmic"
                    }
                },
                "theme":{
                    "id":"theme",
                    "name":"Theme",
                    "options":{
                        "Dark":"dark",
                        "Light":"light"
                    },
                    "refresh":true
                }
            };
			window.$p=true;
			window.$c_usdcny=6.0463;
			setTimeout(function(){
				if(!window.$script_loaded){
					return document.getElementById('loading').innerHTML="<div class=inner>正在加载脚本，请稍后...</div>";
				}
			},1000);
			var goaa=null;
			var waita=seconda=7;
			goaa=setInterval(function(){
				waita--;
				if(waita<0){
					clearInterval(goaa);
					
					waita=seconda;
				}
			},1000);
		}).call(this);
	</script>
	<script type="text/javascript" src="/Public/Home/js/kline.js"></script>
</body>
</html>