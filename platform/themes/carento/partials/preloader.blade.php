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



<div id="preloader-active">
    <div class="preloader d-flex align-items-center justify-content-center">
        <div class="preloader-inner">
            <div class="car-loader-wrap">
                <div class="car-track">
                    <div id="carEl" class="car-el">
                        <svg viewBox="0 0 200 80" xmlns="http://www.w3.org/2000/svg">
                            <ellipse cx="100" cy="76" rx="75" ry="6" fill="rgba(0,0,0,0.12)"/>
                            <rect x="10" y="45" width="180" height="22" rx="6" fill="#c62828"/>
                            <path d="M40 45 Q50 18 80 15 L140 15 Q165 15 175 45 Z" fill="#b71c1c"/>
                            <path d="M130 15 Q155 16 168 42 L140 42 Z" fill="#81d4fa" opacity="0.85"/>
                            <path d="M80 15 L55 42 L90 42 Z" fill="#81d4fa" opacity="0.85"/>
                            <rect x="92" y="16" width="35" height="26" rx="2" fill="#81d4fa" opacity="0.85"/>
                            <rect x="80" y="14" width="60" height="4" rx="2" fill="#8b0000"/>
                            <rect x="175" y="52" width="18" height="8" rx="3" fill="#e53935"/>
                            <rect x="7" y="52" width="18" height="8" rx="3" fill="#e53935"/>
                            <ellipse cx="185" cy="50" rx="7" ry="4" fill="#fff9c4" opacity="0.95"/>
                            <ellipse cx="15" cy="50" rx="6" ry="3.5" fill="#ff1744" opacity="0.9"/>
                            <line x1="105" y1="18" x2="105" y2="64" stroke="#8b0000" stroke-width="1.5"/>
                            <circle cx="45" cy="67" r="16" fill="#1a1a1a"/>
                            <circle cx="45" cy="67" r="10" fill="#333"/>
                            <circle cx="45" cy="67" r="5" fill="#777"/>
                            <line x1="45" y1="57" x2="45" y2="77" stroke="#555" stroke-width="2"/>
                            <line x1="35" y1="67" x2="55" y2="67" stroke="#555" stroke-width="2"/>
                            <circle cx="155" cy="67" r="16" fill="#1a1a1a"/>
                            <circle cx="155" cy="67" r="10" fill="#333"/>
                            <circle cx="155" cy="67" r="5" fill="#777"/>
                            <line x1="155" y1="57" x2="155" y2="77" stroke="#555" stroke-width="2"/>
                            <line x1="145" y1="67" x2="165" y2="67" stroke="#555" stroke-width="2"/>
                            <rect x="8" y="60" width="12" height="5" rx="2" fill="#555"/>
                        </svg>
                    </div>
                    <div class="car-road">
                        <div class="road-lines"></div>
                        <div id="carProgress" class="car-progress"></div>
                    </div>
                </div>
                <p id="carLoadingText" class="car-loading-text">LOADING...</p>
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
    gap: 20px;
}
.car-track {
    position: relative;
    width: 420px;
    height: 130px;
}
.car-el {
    position: absolute;
    bottom: 20px;
    left: 0;
    width: 160px;
    transition: left 0.05s linear;
}
.car-road {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 18px;
    background: #e0e0e0;
    border-radius: 9px;
    overflow: hidden;
}
.road-lines {
    position: absolute;
    top: 7px;
    left: 0;
    width: 200%;
    height: 4px;
    background: repeating-linear-gradient(
        90deg,
        #bbb 0px, #bbb 30px,
        transparent 30px, transparent 50px
    );
    animation: roadMove 0.4s linear infinite;
}
.car-progress {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #c62828, #ff5252);
    border-radius: 9px;
    opacity: 0.4;
}
.car-loading-text {
    font-size: 16px;
    font-weight: 700;
    color: #c62828;
    letter-spacing: 2px;
    margin: 0;
    animation: blink 1s ease-in-out infinite;
}
@keyframes roadMove {
    from { transform: translateX(0); }
    to   { transform: translateX(-80px); }
}
@keyframes blink {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.3; }
}
</style>

<script>
(function() {
    var p = 0;
    function animateCar() {
        p += 0.5;
        if (p > 100) p = 0;
        var maxLeft = 420 - 160;
        var carEl = document.getElementById('carEl');
        var prog = document.getElementById('carProgress');
        var txt = document.getElementById('carLoadingText');
        if (carEl) carEl.style.left = (p / 100 * maxLeft * 0.6) + 'px';
        if (prog) prog.style.width = p + '%';
        if (txt) txt.textContent = 'LOADING' + '.'.repeat(Math.floor(Date.now()/500) % 4);
        requestAnimationFrame(animateCar);
    }
    animateCar();
})();
</script>  
