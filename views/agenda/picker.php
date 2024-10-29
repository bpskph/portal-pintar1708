<link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css'><link rel="stylesheet" href="./style.css">
<h1>flatpickr examples</h1>
<div class="group">
  <input type="text" id="calendar-tomorrow">
  <span class="bar"></span>
  <label class="input-label">From tomorrow</label>
</div>
<div class="group">
  <input type="text" id="calendar-selectrange">
  <span class="bar"></span>
  <label class="input-label">In a week from tomorrow</label>
</div>
<div class="group">
  <input type="text" id="calendar-range">
  <span class="bar"></span>
  <label class="input-label">Range of dates</label>
</div>
<div class="group">
  <input type="text" id="calendar-ja">
  <span class="bar"></span>
  <label class="input-label">Localization (Japanese)</label>
</div>
<style>
    body {
        color: #424242;
    }
    h1 {
        font-size: 1.7em;
        margin: 20px;
    }
    .group {
        position: relative;
        margin: 30px 20px 50px;
    }
    input {
        color: #424242;
        font-size: 1.2em;
        padding: 10px 10px 5px 5px;
        display: block;
        width: 300px;
        border: none;
        border-bottom: 1px solid #607D8B;
    }
    input:focus {
        outline: none;
    }
    label.input-label {
        color: #616161;
        position: absolute;
    }
    input:focus~label.input-label {
        color: #0288D1;
    }
    input[readonly]~label.input-label {
        top: -15px;
        font-size: 0.9em;
    }
    .bar {
        position: relative;
        display: block;
        width: 315px;
    }
    .bar:before,
    .bar:after {
        background: #0288D1;
        content: '';
        height: 2px;
        width: 0;
        bottom: 1px;
        position: absolute;
        transition: 0.2s ease all;
        -moz-transition: 0.2s ease all;
        -webkit-transition: 0.2s ease all;
    }
    .bar:before {
        left: 50%;
    }
    .bar:after {
        right: 50%;
    }
    input:focus~.bar:before,
    input:focus~.bar:after {
        width: 50%;
    }
</style>
<script src='https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.5.1/flatpickr.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js'></script>
<script>
    flatpickr('#calendar-tomorrow', {
        "minDate": new Date().fp_incr(1),
        "enableTime":true
    });
    flatpickr('#calendar-selectrange', {
        "minDate": new Date().fp_incr(1),
        "maxDate": new Date().fp_incr(7)
    });
    flatpickr('#calendar-range', {
        "mode": "range"
    });
    // use "https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ja.js"
    flatpickr('#calendar-ja', {
        "locale": "ja",
        "dateFormat": "Y/m/d",
    });
</script>