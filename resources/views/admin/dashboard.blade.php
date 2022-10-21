@extends('adminlte::page')

@section('title', 'Dashboard')


@section('css')
    <link rel="stylesheet" href="/css/styles.css">
    <style>
        .red-text {
            font-family: Pacifico;
            color: red;
            font-size: 46px;
        }
    
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            /* background-image: url("https://thumbs.gfycat.com/AggravatingUniqueHerald-max-1mb.gif"); */
            /* background-color: black; */
        }
    
        ul, li {
            text-indent: 0;
            text-decoration: none;
            margin: 0;
            padding: 0;
        }
    
        img {
            border: 0;
        }
    
        body {
            background-color: #000;
            color: #999;
            font: 100%/18px helvetica, arial, sans-serif;
        }
    
        canvas {
            cursor: crosshair;
            display: block;
            left: 0;
            position: absolute;
            top: 0;
            z-index: 20;
        }
    
        #header img {
            width: 100%;
            height: 20%;
        }
    
        #bg img {
            width: 100%;
            height: 80%;
        }
    
        #header, #bg {
            position: fixed;
            left: 0;
            right: 0;
            z-index: 10;
        }
    
        #header {
            top: 0;
        }
    
        #bg {
            position: fixed;
            z-index: 1;
            bottom: 0;
        }
    
        audio {
            position: fixed;
            display: none;
            bottom: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 5;
        }
    
        .ak {
            position: absolute;
            left: 0;
            right: 0;
            top: 0%;
            color: #FFE45E;
            text-align: center;
            font-size: 7rem;
            font-style: italic;
            text-shadow: #FFE45E 0 0 30px, #E5C654 1px 0 3px;
        }
    </style>
    <style id="GiftBox">
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }
    
            100% {
                transform: rotate(360deg);
            }
        }
    
        body {
            background-color: lightgrey;
        }
    
        #circle {
            width: 214px;
            height: 214px;
            background-color: #FFC847;
            border-radius: 214px;
            position: fixed;
            top: -50px;
            right: -40px;
            margin: auto;
            box-shadow: 0 1px 2px rgba(0,0,0,0.3);
            transform: scale(.3,.3);
            transition:2s all;
        z-index:1000;
            }
        #circle:hover{
                box-shadow: 0 1px 8px rgba(255,255,255,0.5);
    
            cursor:pointer;
        }
    
            #circle:after {
                content: "";
                display: block;
                border: 7px dashed white;
                width: 200px;
                height: 200px;
                border-radius: 200px;
                animation: spin 30s linear infinite;
            }
    
            #circle:hover {
                background-image: radial-gradient(#FFED85, #FFC847 70%);
            }
    
        #gift {
            position: absolute;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            margin: auto;
            width: 100px;
            height: 125px;
        }
    
        #ribbon {
            position: relative;
            width: 60px;
            height: 60px;
            transform: rotate(45deg);
            margin: auto;
            margin-bottom: -38px;
            border-radius: 0 8px 0 8px;
        }
    
            #ribbon:before {
                content: "";
                height: 24px;
                width: 100%;
                background-color: #F04D4D;
                display: block;
                position: absolute;
                top: 18px;
                box-shadow: 0 0 1px rgba(0,0,0,0.4);
                border-radius: inherit;
            }
    
            #ribbon:after {
                content: "";
                height: 100%;
                width: 24px;
                background-color: #F04D4D;
                display: block;
                margin: auto;
                box-shadow: 0 0 1px rgba(0,0,0,0.4);
                border-radius: inherit;
            }
    
        #giftbox {
            position: relative;
            margin: auto;
            width: 100px;
            height: 100px;
            background-color: white;
            border-radius: 2px;
            box-shadow: 0 0 1px rgba(0,0,0,0.4);
            overflow: hidden;
        }
    
            #giftbox:before {
                content: "";
                height: 24%;
                width: 100%;
                background-color: #F04D4D;
                display: block;
                position: absolute;
                top: 38%;
                box-shadow: inherit;
            }
    
            #giftbox:after {
                content: "";
                height: 100%;
                width: 24%;
                background-color: #F04D4D;
                display: block;
                margin: auto;
                box-shadow: inherit;
            }
    </style>
    <style>
        img{
            position:absolute;top:4px;right:4px;
        }
        
                 #imgB{
                     position:fixed;top:4px;right:4px;
    
                     z-index:10000;
                 }
        #dialogBox{
            position:fixed;
            margin:auto;top:0;bottom:0;left:0;right:0; border:2px dashed rgb(251, 251, 251);
               background-color:rgba(13, 13, 13, 0.90);
            text-align:center;
            color:#FFC847;
            padding:20px;
            padding-top:40px;
            height:400px;width:600px;
            transform:rotateX(90deg);
                 cursor:default;
       transition:.8s;
        }
        #cover{
      
                 position:fixed;
           top:0px;left:0px;
           z-index:1;
           background:rgba(117,133,247, 0.3);
          height:100vh;width:100vw;
       opacity:0;
       transition:1s;
        }
    
        @media screen and (max-width: 720px) {
              #circle:hover{
                box-shadow: 0 1px 8px rgba(255,255,255,0.5);
    
            cursor:pointer;
        }
                #dialogBox {
                      height:100%;width:100%;
              padding:0%;
              border:0px;
    
                      padding-top:80%;
    
              background-color:rgba(13, 13, 13, 0.90);
            }
            #cover{
                      height:100%;width:100%;
            }
                 #circle{
                     bottom:0px;left:-30px;
                 }
                 img{
                     position:fixed;top:4px;left:4px;
    
                 }
    
                 #imgB{
                     position:relative;top:4px;left:4px;
    
                     z-index:10000;
                 }
    }
    
    </style>
    <style>
        *{
            -ms-user-select:none;
            -moz-user-select:none;
            -o-user-select:none;
            user-select:none;
            -webkit-user-select:none;
        }
      </style>
      <style>
      body {
        /*   background-color: rgb(241, 241, 241); */
          /* background-color:black; */
        } 
        .grid{
            display: grid;
            gap: 2rem;
            grid-template-columns: repeat(4, 1fr);
            margin-top: 3rem;
        }
        h1
        {
        /*   margin-top:200px; */
          color:black;
        }
        .lamp {
          position: relative;
          top: 170px;
          margin: 0 auto;
          height: 70px;
          width: 70px;
          background-color: brow;
          border-radius: 0px 10px 100% 0px;
          border-bottom: 3px solid rgb(144, 17, 58);
          box-shadow: inset 0 -50px 50px -20px rgb(144, 17, 58);
        }
        
        .lamp:before {
          content: '';
          display: block;
          height: 70px;
          width: 70px;
          margin-left: -70px;
          background-color: lightb;
          border-radius: 10px 0px 0px 100%;
          box-shadow: inset 0 -50px 50px -20px rgb(144, 17, 58);
          border-bottom: 3px solid rgb(144, 17, 58);
        }
        
        .flame {
          position: absolute;
          top: -65px;
          left: -30px;
          width: 60px;
          height: 60px;
          background: OrangeRed;
          border-radius: 0 110px 20px 110px;
          transform: rotate(45deg);
          -webkit-transform: rotate(45deg);
          -moz-transform: rotate(45deg);
        }
        
        .flame:after {
          content: '';
          display: block;
          width: 60px;
          height: 60px;
          background: OrangeRed;
          border-radius: 0 110px 20px 110px;
          transform: rotate(0deg);
          -webkit-transform: rotate(0deg);
          -moz-transform: rotate(0deg);
          animation: flame_glare 1.5s linear infinite;
          -webkit-animation: flame_glare 1.5s linear infinite;
          -moz-animation: flame_glare 1.5s linear infinite;
        }
        
        .top {
          position: absolute;
          top: -40px;
          left: -70px;
          height: 85px;
          width: 120px;
          background-color: brown;
          border-radius: 100%;
          border-left: 10px solid rgb(144, 17, 58);
          border-right: 10px solid rgb(144, 17, 58);
          transform: rotateX(-60deg);
          -webkit-transform: rotateX(-60deg);
          -moz-transform: rotateX(-60deg);
          background: radial-gradient(OrangeRed, rgb(214, 23, 92) 40%, rgb(144, 17, 58));
          background: -webkit-radial-gradient(OrangeRed, rgb(214, 23, 92) 40%, rgb(144, 17, 58));
          background: -moz-radial-gradient(OrangeRed, rgb(214, 23, 92) 40%, rgb(144, 17, 58));
        }
        
        @keyframes flame_glare {
          0% {
            transform: scale(0);
            opacity: 0;
          }
          50% {
            transform: scale(1.3);
            opacity: 0.5;
          }
          100% {
            transform: scale(1.5);
            opacity: 0;
          }
        }
        @keyframes glow
        {
            0% { text-shadow: 0 0 10px violet, 0 0 20px #fff, 0 0 30px #EAB72F, 0 0 40px #e60073, 0 0 50px #e60073, 0 0 60px #EAB72F, 0 0 70px #EA5C2F}
            25% { text-shadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px #fafa05, 0 0 40px #fafa05, 0 0 50px #2fa9ea, 0 0 60px #2fa9ea, 0 0 70px }
            50% { textShadow: 0 0 10px #fff, 0 0 20px #fff, 0 0 30px #e60073, 0 0 40px #e60073, 0 0 50px #362FEA, 0 0 60px #e60073, 0 0 70px #362FEA}
            100% { textShadow: 0 0 10px #fff, 0 0 20px #ff4da6, 0 0 30px #362FEA, 0 0 40px #EA5C2F, 0 0 50px #EAB72F, 0 0 60px #ff4da6, 0 0 70px #ff4da6}
        }
        
        .glow {
            font-size: 80px;
            color: #fff;
            text-align: center;
            animation: glow 1s infinite ease-in-out alternate;
        }
    </style>        
@stop

@section('content_header')
<audio type="audio/mpeg" controls="none" autoplay id="audio1" src="https://virtualgadgets.in/diwali/fire.mp3"></audio>
    <h1 class="m-0 text-dark">Admin Dashboard</h1>
   
    @stop
    
    @section('content')

    </div>
    <div class="container-fluid">
        <br />
        <br />
        </div>
        
        <h1 class="glow" style="color:rgb(3, 219, 57)">Happy Diwali</h1>
       

@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-compat/3.0.0-alpha1/jquery.min.js"></script>
<script>
    function ak() {

        document.getElementById('dialogBox').style.transform = "rotateX(0deg)";
        document.getElementById('cover').style.zIndex = "1001";
      //  document.getElementById('cover').style.display = "block";

        document.getElementById('cover').style.opacity = "1";
    
    }
    

    function bk() {
        document.getElementById('dialogBox').style.transform = "rotateX(90deg)";
        document.getElementById('cover').style.zIndex = "0";
    //    document.getElementById('cover').style.display = "none";
        document.getElementById('cover').style.opacity = "0";
    }

    function onLoad()
    {
        function getParams() {
            var idx = document.URL.indexOf('?');
            var params = {}; // simple js object
            firstname = unescape(params["FirstName"]);
            alert(firstname);
        }
    }
</script>
<script>


    $(function () {

        var Fireworks = function () {

var styles = [
'background: linear-gradient(#D33106, #571402)'
, 'border: 1px solid #3E0E02'
, 'color: white'
, 'display: block'
, 'text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3)'
, 'box-shadow: 0 1px 0 rgba(255, 255, 255, 0.4) inset, 0 5px 3px -5px rgba(0, 0, 0, 0.5), 0 -13px 5px -10px rgba(255, 255, 255, 0.4) inset'
, 'line-height: 40px'
, 'text-align: center'
, 'font-weight: bold'
].join(';');


            var self = this;
            var rand = function (rMi, rMa) { return ~~((Math.random() * (rMa - rMi + 1)) + rMi); }
            var hitTest = function (x1, y1, w1, h1, x2, y2, w2, h2) { return !(x1 + w1 < x2 || x2 + w2 < x1 || y1 + h1 < y2 || y2 + h2 < y1); };
            window.requestAnimFrame = function () { return window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame || window.oRequestAnimationFrame || window.msRequestAnimationFrame || function (a) { window.setTimeout(a, 1E3 / 60) } }();

            self.init = function () {
               
              

                self.canvas = document.createElement('canvas');
                self.canvas.width = self.cw = $(window).innerWidth();
                self.canvas.height = self.ch = $(window).innerHeight();
                self.particles = [];
                self.partCount = 150;
                self.fireworks = [];
                self.mx = self.cw / 2;
                self.my = self.ch / 2;
                self.currentHue = 30;
                self.partSpeed = 5;
                self.partSpeedVariance = 10;
                self.partWind = 50;
                self.partFriction = 5;
                self.partGravity = 1;
                self.hueMin = 0;
                self.hueMax = 360;
                self.fworkSpeed = 4;
                self.fworkAccel = 10;
                self.hueVariance = 30;
                self.flickerDensity = 25;
                self.showShockwave = true;
                self.showTarget = false;
                self.clearAlpha = 25;
                $(document.body).append(self.canvas);
                self.ctx = self.canvas.getContext('2d');
                self.ctx.lineCap = 'round';
                self.ctx.lineJoin = 'round';
                self.lineWidth = 1;
                self.bindEvents();
                self.canvasLoop();

                self.canvas.onselectstart = function () {

                    return false;
                };
            };

            self.createParticles = function (x, y, hue) {
                var audio = document.getElementById('audio1');
                if (audio.paused) {
                    audio.play();
                } else {
                    audio.currentTime = 0
                }
                var countdown = self.partCount;
                while (countdown--) {
                    var newParticle = {
                        x: x,
                        y: y,
                        coordLast: [
                            { x: x, y: y },
                            { x: x, y: y },
                            { x: x, y: y }
                        ],
                        angle: rand(0, 360),
                        speed: rand(((self.partSpeed - self.partSpeedVariance) <= 0) ? 1 : self.partSpeed - self.partSpeedVariance, (self.partSpeed + self.partSpeedVariance)),
                        friction: 1 - self.partFriction / 100,
                        gravity: self.partGravity / 2,
                        hue: rand(hue - self.hueVariance, hue + self.hueVariance),
                        brightness: rand(50, 80),
                        alpha: rand(40, 100) / 100,
                        decay: rand(10, 50) / 1000,
                        wind: (rand(0, self.partWind) - (self.partWind / 2)) / 25,
                        lineWidth: self.lineWidth
                    };
                    self.particles.push(newParticle);
                }
            };


            self.updateParticles = function () {
                var i = self.particles.length;
                while (i--) {
                    var p = self.particles[i];
                    var radians = p.angle * Math.PI / 180;
                    var vx = Math.cos(radians) * p.speed;
                    var vy = Math.sin(radians) * p.speed;
                    p.speed *= p.friction;

                    p.coordLast[2].x = p.coordLast[1].x;
                    p.coordLast[2].y = p.coordLast[1].y;
                    p.coordLast[1].x = p.coordLast[0].x;
                    p.coordLast[1].y = p.coordLast[0].y;
                    p.coordLast[0].x = p.x;
                    p.coordLast[0].y = p.y;

                    p.x += vx;
                    p.y += vy;
                    p.y += p.gravity;

                    p.angle += p.wind;
                    p.alpha -= p.decay;

                    if (!hitTest(0, 0, self.cw, self.ch, p.x - p.radius, p.y - p.radius, p.radius * 2, p.radius * 2) || p.alpha < .05) {
                        self.particles.splice(i, 1);
                    }
                };
            };

            self.drawParticles = function () {
                var i = self.particles.length;
                while (i--) {
                    var p = self.particles[i];

                    var coordRand = (rand(1, 3) - 1);
                    self.ctx.beginPath();
                    self.ctx.moveTo(Math.round(p.coordLast[coordRand].x), Math.round(p.coordLast[coordRand].y));
                    self.ctx.lineTo(Math.round(p.x), Math.round(p.y));
                    self.ctx.closePath();
                    self.ctx.strokeStyle = 'hsla(' + p.hue + ', 100%, ' + p.brightness + '%, ' + p.alpha + ')';
                    self.ctx.stroke();

                    if (self.flickerDensity > 0) {
                        var inverseDensity = 50 - self.flickerDensity;
                        if (rand(0, inverseDensity) === inverseDensity) {
                            self.ctx.beginPath();
                            self.ctx.arc(Math.round(p.x), Math.round(p.y), rand(p.lineWidth, p.lineWidth + 3) / 2, 0, Math.PI * 2, false)
                            self.ctx.closePath();
                            var randAlpha = rand(50, 100) / 100;
                            self.ctx.fillStyle = 'hsla(' + p.hue + ', 100%, ' + p.brightness + '%, ' + randAlpha + ')';
                            self.ctx.fill();
                        }
                    }
                };
            };


            self.createFireworks = function (startX, startY, targetX, targetY) {
                var newFirework = {
                    x: startX,
                    y: startY,
                    startX: startX,
                    startY: startY,
                    hitX: false,
                    hitY: false,
                    coordLast: [
                        { x: startX, y: startY },
                        { x: startX, y: startY },
                        { x: startX, y: startY }
                    ],
                    targetX: targetX,
                    targetY: targetY,
                    speed: self.fworkSpeed,
                    angle: Math.atan2(targetY - startY, targetX - startX),
                    shockwaveAngle: Math.atan2(targetY - startY, targetX - startX) + (90 * (Math.PI / 180)),
                    acceleration: self.fworkAccel / 100,
                    hue: self.currentHue,
                    brightness: rand(50, 80),
                    alpha: rand(50, 100) / 100,
                    lineWidth: self.lineWidth
                };
                self.fireworks.push(newFirework);

            };


            self.updateFireworks = function () {
                var i = self.fireworks.length;

                while (i--) {
                    var f = self.fireworks[i];
                    self.ctx.lineWidth = f.lineWidth;

                    vx = Math.cos(f.angle) * f.speed,
                    vy = Math.sin(f.angle) * f.speed;
                    f.speed *= 1 + f.acceleration;
                    f.coordLast[2].x = f.coordLast[1].x;
                    f.coordLast[2].y = f.coordLast[1].y;
                    f.coordLast[1].x = f.coordLast[0].x;
                    f.coordLast[1].y = f.coordLast[0].y;
                    f.coordLast[0].x = f.x;
                    f.coordLast[0].y = f.y;

                    if (f.startX >= f.targetX) {
                        if (f.x + vx <= f.targetX) {
                            f.x = f.targetX;
                            f.hitX = true;
                        } else {
                            f.x += vx;
                        }
                    } else {
                        if (f.x + vx >= f.targetX) {
                            f.x = f.targetX;
                            f.hitX = true;
                        } else {
                            f.x += vx;
                        }
                    }

                    if (f.startY >= f.targetY) {
                        if (f.y + vy <= f.targetY) {
                            f.y = f.targetY;
                            f.hitY = true;
                        } else {
                            f.y += vy;
                        }
                    } else {
                        if (f.y + vy >= f.targetY) {
                            f.y = f.targetY;
                            f.hitY = true;
                        } else {
                            f.y += vy;
                        }
                    }

                    if (f.hitX && f.hitY) {
                        self.createParticles(f.targetX, f.targetY, f.hue);
                        self.fireworks.splice(i, 1);

                    }
                };
            };

            self.drawFireworks = function () {
                var i = self.fireworks.length;
                self.ctx.globalCompositeOperation = 'lighter';
                while (i--) {
                    var f = self.fireworks[i];
                    self.ctx.lineWidth = f.lineWidth;

                    var coordRand = (rand(1, 3) - 1);
                    self.ctx.beginPath();
                    self.ctx.moveTo(Math.round(f.coordLast[coordRand].x), Math.round(f.coordLast[coordRand].y));
                    self.ctx.lineTo(Math.round(f.x), Math.round(f.y));
                    self.ctx.closePath();
                    self.ctx.strokeStyle = 'hsla(' + f.hue + ', 100%, ' + f.brightness + '%, ' + f.alpha + ')';
                    self.ctx.stroke();

                    if (self.showTarget) {
                        self.ctx.save();
                        self.ctx.beginPath();
                        self.ctx.arc(Math.round(f.targetX), Math.round(f.targetY), rand(1, 8), 0, Math.PI * 2, false)
                        self.ctx.closePath();
                        self.ctx.lineWidth = 1;
                        self.ctx.stroke();
                        self.ctx.restore();
                    }

                    if (self.showShockwave) {
                        self.ctx.save();
                        self.ctx.translate(Math.round(f.x), Math.round(f.y));
                        self.ctx.rotate(f.shockwaveAngle);
                        self.ctx.beginPath();
                        self.ctx.arc(0, 0, 1 * (f.speed / 5), 0, Math.PI, true);
                        self.ctx.strokeStyle = 'hsla(' + f.hue + ', 100%, ' + f.brightness + '%, ' + rand(25, 60) / 100 + ')';
                        self.ctx.lineWidth = f.lineWidth;
                        self.ctx.stroke();
                        self.ctx.restore();
                    }
                };
            };

            self.bindEvents = function () {
                $(window).on('resize', function () {
                    clearTimeout(self.timeout);
                    self.timeout = setTimeout(function () {
                        self.canvas.width = self.cw = $(window).innerWidth();
                        self.canvas.height = self.ch = $(window).innerHeight();
                        self.ctx.lineCap = 'round';
                        self.ctx.lineJoin = 'round';
                    }, 100);
                });

                $(self.canvas).on('mousedown', function (e) {
                    self.mx = e.pageX - self.canvas.offsetLeft;
                    self.my = e.pageY - self.canvas.offsetTop;
                    self.currentHue = rand(self.hueMin, self.hueMax);
                    self.createFireworks(self.cw / 2, self.ch, self.mx, self.my);

                    $(self.canvas).on('mousemove.fireworks', function (e) {
                        self.mx = e.pageX - self.canvas.offsetLeft;
                        self.my = e.pageY - self.canvas.offsetTop;
                        self.currentHue = rand(self.hueMin, self.hueMax);
                        self.createFireworks(self.cw / 2, self.ch, self.mx, self.my);
                    });
                });

                $(self.canvas).on('mouseup', function (e) {
                    $(self.canvas).off('mousemove.fireworks');
                });

            }

            self.clear = function () {
                self.particles = [];
                self.fireworks = [];
                self.ctx.clearRect(0, 0, self.cw, self.ch);
            };


            self.canvasLoop = function () {
                requestAnimFrame(self.canvasLoop, self.canvas);
                self.ctx.globalCompositeOperation = 'destination-out';
                self.ctx.fillStyle = 'rgba(0,0,0,' + self.clearAlpha / 100 + ')';
                self.ctx.fillRect(0, 0, self.cw, self.ch);
                self.updateFireworks();
                self.updateParticles();
                self.drawFireworks();
                self.drawParticles();

            };

            self.init();

        }



        var fworks = new Fireworks();

        $('#info-toggle').on('click', function (e) {
            $('#info-inner').stop(false, true).slideToggle(100);
            e.preventDefault();
        });

    });
</script>

@stop