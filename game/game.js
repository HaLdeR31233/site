var myCar;
var obstacles = [];
var coins = [];
var score = 0;
var lives = 3;
var gameSpeed = 1;
var obstacleSpeed = 2;
var coinSpeed = 1.5;
var gameRunning = true;
var lastObstacleTime = 0;
var lastCoinTime = 0;

var myGameArea = {
    canvas : document.getElementById("gameCanvas"),
    start : function() {
        this.canvas.width = 480;
        this.canvas.height = 270;
        this.context = this.canvas.getContext("2d");
    },
    clear : function() {
        this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);
    },
    update : function() {
        this.clear();
        if (gameRunning) {
            myCar.update();
            updateObstacles();
            updateCoins();
            checkCollisions();
            updateScore();
        }
    }
}

class Car {
    constructor(width, height, color, x, y) {
        this.width = width;
        this.height = height;
        this.color = color;
        this.x = x;
        this.y = y;
        this.speed = 0;
        this.angle = 0;
        this.maxSpeed = 3;
        this.acceleration = 0.1;
        this.friction = 0.05;
    }

    update() {
        const ctx = myGameArea.context;
        
        this.speed *= (1 - this.friction);
        
        if (this.speed > this.maxSpeed) this.speed = this.maxSpeed;
        if (this.speed < -this.maxSpeed) this.speed = -this.maxSpeed;
        
        this.x += Math.cos(this.angle) * this.speed;
        this.y += Math.sin(this.angle) * this.speed;
        
        if (this.x < 0) this.x = 0;
        if (this.x > myGameArea.canvas.width - this.width) this.x = myGameArea.canvas.width - this.width;
        if (this.y < 0) this.y = 0;
        if (this.y > myGameArea.canvas.height - this.height) this.y = myGameArea.canvas.height - this.height;
        
        ctx.save();
        
        ctx.translate(this.x + this.width/2, this.y + this.height/2);
        
        ctx.rotate(this.angle);
        
        ctx.fillStyle = this.color;
        ctx.fillRect(-this.width/2, -this.height/2, this.width, this.height);
        
        ctx.fillStyle = "#87CEEB";
        ctx.fillRect(-this.width/2 + 5, -this.height/2 + 3, this.width - 10, this.height - 6);
        
        ctx.fillStyle = "#333";
        ctx.fillRect(-this.width/2 - 2, -this.height/2 - 2, 4, 4);
        ctx.fillRect(this.width/2 - 2, -this.height/2 - 2, 4, 4);
        ctx.fillRect(-this.width/2 - 2, this.height/2 - 2, 4, 4);
        ctx.fillRect(this.width/2 - 2, this.height/2 - 2, 4, 4);
        
        ctx.restore();
    }
    
    accelerate() {
        this.speed += this.acceleration;
    }
    
    brake() {
        this.speed -= this.acceleration;
    }
    
    turnLeft() {
        this.angle -= 0.1;
    }
    
    turnRight() {
        this.angle += 0.1;
    }
    
    honk() {
        try {
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const oscillator = audioContext.createOscillator();
            const gainNode = audioContext.createGain();
            
            oscillator.connect(gainNode);
            gainNode.connect(audioContext.destination);
            
            oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
            oscillator.frequency.setValueAtTime(600, audioContext.currentTime + 0.1);
            oscillator.frequency.setValueAtTime(400, audioContext.currentTime + 0.2);
            
            gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
            
            oscillator.type = 'sawtooth';
            
            oscillator.start(audioContext.currentTime);
            oscillator.stop(audioContext.currentTime + 0.3);
            
        } catch (error) {
            console.log('Не вдалося відтворити звук клаксона:', error);
        }
    }
}

class Obstacle {
    constructor(x, y, width, height, color) {
        this.x = x;
        this.y = y;
        this.width = width;
        this.height = height;
        this.color = color;
        this.speed = obstacleSpeed;
    }
    
    update() {
        this.y += this.speed;
    }
    
    draw() {
        const ctx = myGameArea.context;
        ctx.fillStyle = this.color;
        ctx.fillRect(this.x, this.y, this.width, this.height);
        
        ctx.fillStyle = "#8B4513";
        ctx.fillRect(this.x + 5, this.y + 5, this.width - 10, this.height - 10);
    }
}

class Coin {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.radius = 8;
        this.speed = coinSpeed;
        this.rotation = 0;
    }
    
    update() {
        this.y += this.speed;
        this.rotation += 0.2;
    }
    
    draw() {
        const ctx = myGameArea.context;
        ctx.save();
        ctx.translate(this.x + this.radius, this.y + this.radius);
        ctx.rotate(this.rotation);
        
        ctx.fillStyle = "#FFD700";
        ctx.beginPath();
        ctx.arc(0, 0, this.radius, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.fillStyle = "#FFA500";
        ctx.beginPath();
        ctx.arc(0, 0, this.radius - 3, 0, Math.PI * 2);
        ctx.fill();
        
        ctx.fillStyle = "#FFD700";
        ctx.font = "12px Arial";
        ctx.textAlign = "center";
        ctx.fillText("C", 0, 4);
        
        ctx.restore();
    }
}

function updateObstacles() {
    const currentTime = Date.now();
    
    if (currentTime - lastObstacleTime > 2000) {
        const x = Math.random() * (myGameArea.canvas.width - 40);
        const colors = ["#e74c3c", "#f39c12", "#9b59b6", "#34495e"];
        const color = colors[Math.floor(Math.random() * colors.length)];
        obstacles.push(new Obstacle(x, -30, 40, 30, color));
        lastObstacleTime = currentTime;
    }
    
    for (let i = obstacles.length - 1; i >= 0; i--) {
        obstacles[i].update();
        obstacles[i].draw();
        
        if (obstacles[i].y > myGameArea.canvas.height) {
            obstacles.splice(i, 1);
        }
    }
}

function updateCoins() {
    const currentTime = Date.now();
    
    if (currentTime - lastCoinTime > 1500) {
        const x = Math.random() * (myGameArea.canvas.width - 16);
        coins.push(new Coin(x, -20));
        lastCoinTime = currentTime;
    }
    
    for (let i = coins.length - 1; i >= 0; i--) {
        coins[i].update();
        coins[i].draw();
        
        if (coins[i].y > myGameArea.canvas.height) {
            coins.splice(i, 1);
        }
    }
}

function checkCollisions() {
    for (let i = obstacles.length - 1; i >= 0; i--) {
        if (isColliding(myCar, obstacles[i])) {
            lives--;
            obstacles.splice(i, 1);
            updateLivesDisplay();
            
            if (lives <= 0) {
                gameOver();
            }
        }
    }
    
    for (let i = coins.length - 1; i >= 0; i--) {
        if (isColliding(myCar, coins[i])) {
            score += 10;
            coins.splice(i, 1);
            updateScoreDisplay();
        }
    }
}

function isColliding(obj1, obj2) {
    if (obj2.radius) {
        const dx = (obj1.x + obj1.width/2) - (obj2.x + obj2.radius);
        const dy = (obj1.y + obj1.height/2) - (obj2.y + obj2.radius);
        const distance = Math.sqrt(dx * dx + dy * dy);
        return distance < (obj1.width/2 + obj2.radius);
    } else {
        return obj1.x < obj2.x + obj2.width &&
               obj1.x + obj1.width > obj2.x &&
               obj1.y < obj2.y + obj2.height &&
               obj1.y + obj1.height > obj2.y;
    }
}

function updateScore() {
    score += 0.1;
    updateScoreDisplay();
}

function updateScoreDisplay() {
    const scoreElement = document.getElementById('score');
    if (scoreElement) {
        scoreElement.textContent = Math.floor(score);
    }
}

function updateLivesDisplay() {
    const livesElement = document.getElementById('lives');
    if (livesElement) {
        livesElement.textContent = lives;
    }
}

function gameOver() {
    gameRunning = false;
    const gameOverElement = document.getElementById('gameOver');
    const finalScoreElement = document.getElementById('finalScore');
    if (gameOverElement && finalScoreElement) {
        finalScoreElement.textContent = Math.floor(score);
        gameOverElement.style.display = 'block';
    }
}

function restartGame() {
    gameRunning = true;
    score = 0;
    lives = 3;
    obstacles = [];
    coins = [];
    lastObstacleTime = 0;
    lastCoinTime = 0;
    
    myCar.x = 240;
    myCar.y = 200;
    myCar.speed = 0;
    myCar.angle = 0;
    
    updateScoreDisplay();
    updateLivesDisplay();
    
    const gameOverElement = document.getElementById('gameOver');
    if (gameOverElement) {
        gameOverElement.style.display = 'none';
    }
}

const keys = {
    up: false,
    down: false,
    left: false,
    right: false,
    honk: false
};

document.addEventListener('keydown', (event) => {
    switch(event.key) {
        case 'ArrowUp':
        case 'w':
        case 'W':
        case 'ц':
        case 'Ц':
            keys.up = true;
            break;
        case 'ArrowDown':
        case 's':
        case 'S':
        case 'ы':
        case 'Ы':
            keys.down = true;
            break;
        case 'ArrowLeft':
        case 'a':
        case 'A':
        case 'ф':
        case 'Ф':
            keys.left = true;
            break;
        case 'ArrowRight':
        case 'd':
        case 'D':
        case 'в':
        case 'В':
            keys.right = true;
            break;
        case ' ':
        case 'h':
        case 'H':
            if (!keys.honk) {
                keys.honk = true;
                myCar.honk();
            }
            break;
    }
});

document.addEventListener('keyup', (event) => {
    switch(event.key) {
        case 'ArrowUp':
        case 'w':
        case 'W':
        case 'ц':
        case 'Ц':
            keys.up = false;
            break;
        case 'ArrowDown':
        case 's':
        case 'S':
        case 'ы':
        case 'Ы':
            keys.down = false;
            break;
        case 'ArrowLeft':
        case 'a':
        case 'A':
        case 'ф':
        case 'Ф':
            keys.left = false;
            break;
        case 'ArrowRight':
        case 'd':
        case 'D':
        case 'в':
        case 'В':
            keys.right = false;
            break;
        case ' ':
        case 'h':
        case 'H':
            keys.honk = false;
            break;
    }
});

function gameLoop() {
    if (keys.up) {
        myCar.accelerate();
    }
    if (keys.down) {
        myCar.brake();
    }
    if (keys.left) {
        myCar.turnLeft();
    }
    if (keys.right) {
        myCar.turnRight();
    }
    
    myGameArea.update();
    
    requestAnimationFrame(gameLoop);
}

window.onload = () => {
    myGameArea.start();
    myCar = new Car(40, 20, "red", 240, 200);
    myGameArea.canvas.focus();
    updateScoreDisplay();
    updateLivesDisplay();
    gameLoop();
}

document.addEventListener('click', (event) => {
    if (event.target === myGameArea.canvas) {
        myGameArea.canvas.focus();
    }
});