<!-- <div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner position-relative">
            <div class="text-center">
                <div class="page-loader"></div>
            </div>
        </div>
    </div>
</div> -->
<!-- 
<div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content: center">
        <div class="preloader-inner position-relative">
            <div class="car-loader-wrap">
                <div class="car-road">
                    <div class="car-progress-bar">
                        <div class="car-progress-fill"></div>
                    </div>
                    <div class="car-svg">
                        <svg viewBox="0 0 100 40" xmlns="http://www.w3.org/2000/svg">
                            <g fill="#c62828">
                                <rect x="5" y="20" width="90" height="12" rx="3"/>
                                <rect x="20" y="10" width="55" height="15" rx="4"/>
                                <rect x="25" y="8" width="20" height="10" rx="3"/>
                                <rect x="50" y="8" width="20" height="10" rx="3"/>
                            </g>
                            <circle cx="22" cy="33" r="7" fill="#222"/>
                            <circle cx="22" cy="33" r="3.5" fill="#888"/>
                            <circle cx="78" cy="33" r="7" fill="#222"/>
                            <circle cx="78" cy="33" r="3.5" fill="#888"/>
                        </svg>
                    </div>
                </div>
                <p class="car-loading-text">Loading...</p>
            </div>
        </div>
    </div>
</div>

<style>
#preloader-active {
    position: fixed;
    inset: 0;
    background: #ffffff;
    z-index: 99999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preloader {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.car-loader-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}

.car-road {
    position: relative;
    width: 320px;
}

.car-svg {
    position: absolute;
    width: 110px;
    bottom: 10px;
    left: 0;
    animation: carDrive 2.5s ease-in-out infinite;
    filter: drop-shadow(0 4px 8px rgba(198,40,40,0.3));
}

.car-progress-bar {
    width: 100%;
    height: 10px;
    background: #f0f0f0;
    border-radius: 10px;
    margin-top: 60px;
    overflow: hidden;
    border: 1.5px solid #e0e0e0;
}

.car-progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #c62828, #ff5252);
    border-radius: 10px;
    animation: progressFill 2.5s ease-in-out infinite;
}

.car-loading-text {
    font-size: 15px;
    font-weight: 600;
    color: #c62828;
    letter-spacing: 1px;
    margin: 0;
    animation: blink 1.2s ease-in-out infinite;
}

@keyframes carDrive {
    0%   { left: 0%; }
    100% { left: calc(100% - 110px); }
}

@keyframes progressFill {
    0%   { width: 0%; }
    100% { width: 100%; }
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.4; }
}
</style>  -->


<style>
  /* === OVERLAY - dark transparent === */
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
    border: 3px dashed #00ff00;
    animation: spin 2s linear infinite;
  }

  .core {
    position: absolute;
    width: 60%;
    height: 60%;
    top: 20%;
    left: 20%;
    background: rgba(0, 255, 0, 0.1);
    border-radius: 50%;
    animation: glitch-core 0.5s infinite;
    box-shadow: 0 0 25px #00ff00;
  }

  .binary-digits {
    position: absolute;
    width: 100%;
    height: 100%;
    color: #00ff00;
    font-size: 18px;     /* bde digits */
    text-align: center;
    animation: spin 1.5s linear infinite reverse;
  }

  .binary-digits span {
    position: absolute;
    top: 0;
    left: 50%;
    transform-origin: 0 90px;   /* 180px/2 = 90px */
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
    color: #00ff00;
    font-size: 20px;     /* bda text */
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