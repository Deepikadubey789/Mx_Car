
<style>
  .hacker-loader-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.75);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 9999999;
    opacity: 1;
    transition: opacity 0.5s ease;
  }

  /* === LOADER - bda, centered === */
  .hacker-loader {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 180px;      /* 100px tha, ab 180px */
    height: 180px;
    z-index: 99999999;
  }

  .loader-hidden {
    opacity: 0;
    pointer-events: none;
  }

  .loader-gone {
    display: none !important;
  }

  .binary-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px dashed #df4827 !important;
    animation: spin 2s linear infinite;
  }

  .core {
    position: absolute;
    width: 60%;
    height: 60%;
    top: 20%;
    left: 20%;
    background: linear-gradient(135deg, #B03A2E 0%, #8E2B21 100%);
    border-radius: 50%;
    animation: glitch-core 0.5s infinite;
    box-shadow: 0 0 25px #df4827 !important;
  }

  .binary-digits {
    position: absolute;
    width: 100%;
    height: 100%;
    color: #df4827 !important;
    font-size: 18px;     
    text-align: center;
    animation: spin 1.5s linear infinite reverse;
  }

  .binary-digits span {
    position: absolute;
    top: 0;
    left: 50%;
    transform-origin: 0 90px;  
  }

  .binary-digits span:nth-child(1) { transform: rotate(0deg)   translateY(-10px); }
  .binary-digits span:nth-child(2) { transform: rotate(45deg)  translateY(-10px); }
  .binary-digits span:nth-child(3) { transform: rotate(90deg)  translateY(-10px); }
  .binary-digits span:nth-child(4) { transform: rotate(135deg) translateY(-10px); }
  .binary-digits span:nth-child(5) { transform: rotate(180deg) translateY(-10px); }
  .binary-digits span:nth-child(6) { transform: rotate(225deg) translateY(-10px); }
  .binary-digits span:nth-child(7) { transform: rotate(270deg) translateY(-10px); }
  .binary-digits span:nth-child(8) { transform: rotate(315deg) translateY(-10px); }

  .loading-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color:rgb(230, 230, 230) !important;
    font-size: 20px;   
    text-transform: uppercase;
    animation: flicker 1.5s infinite;
    white-space: nowrap;
  }

  @keyframes spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  @keyframes glitch-core {
    0%   { transform: scale(1); }
    20%  { transform: scale(1.05) translate(2px, -2px); }
    40%  { transform: scale(0.95) translate(-2px, 2px); }
    60%  { transform: scale(1.02) translate(1px, 1px); }
    100% { transform: scale(1); }
  }

  @keyframes flicker {
    0%, 19%, 21%, 23%, 25%, 54%, 56%, 100% { opacity: 1; }
    20%, 24%, 55% { opacity: 0.3; }
  }
</style>

<!-- DARK TRANSPARENT BACKDROP -->
<div class="hacker-loader-overlay" id="hackerOverlay"></div>

<!-- LOADER -->
<div class="hacker-loader" id="hackerLoader">
  <div class="binary-ring"></div>
  <div class="core"></div>
  <div class="binary-digits">
    <span>0</span>
    <span>1</span>
    <span>0</span>
    <span>1</span>
    <span>1</span>
    <span>0</span>
    <span>1</span>
    <span>0</span>
  </div>
  <div class="loading-text">Loading</div>
</div>

<script>
  window.addEventListener('load', function () {
    var overlay = document.getElementById('hackerOverlay');
    var loader  = document.getElementById('hackerLoader');

    overlay.classList.add('loader-hidden');
    loader.classList.add('loader-hidden');

    setTimeout(function () {
      overlay.classList.add('loader-gone');
      loader.classList.add('loader-gone');
    }, 500);
  });
</script>