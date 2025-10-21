<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Завдання 6</title>
<style>
    body{
        padding: 0;
        margin: 0 auto;
        display: grid;
        height: 100vh;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-family: Arial, sans-serif;
    }
    .game-container {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
    }
    canvas {
        border: 3px solid #fff;
        background-color: #2c3e50;
        box-shadow: 0 0 20px rgba(0,0,0,0.5);
    }
    .game-info {
        background: rgba(255,255,255,0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.3);
        min-width: 250px;
    }
    .score {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    .lives {
        font-size: 18px;
        color: #e74c3c;
        margin-bottom: 15px;
    }
    .controls {
        font-size: 14px;
        color: #34495e;
    }
    .controls h3 {
        margin-top: 0;
        color: #2c3e50;
    }
    .game-over {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 30px;
        border-radius: 10px;
        text-align: center;
        display: none;
    }
    .restart-btn {
        background: #3498db;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        margin-top: 15px;
    }
    .restart-btn:hover {
        background: #2980b9;
    }
</style>
</head>
<body>
    <div class="game-container">
        <canvas id="gameCanvas" width="480" height="270" tabindex="0"></canvas>
        <div class="game-info">
            <div class="score">Очки: <span id="score">0</span></div>
            <div class="lives">Жизни: <span id="lives">3</span></div>
            <div class="controls">
                <h3>🎮 Управление:</h3>
                <ul>
                    <li>↑/W/Ц - Газ</li>
                    <li>↓/S/Ы - Тормоз</li>
                    <li>←/A/Ф - Влево</li>
                    <li>→/D/В - Вправо</li>
                    <li>Пробел/H - Сигнал</li>
                </ul>
                <h3>🎯 Цель:</h3>
                <p>Собирайте монеты, избегайте препятствий!</p>
            </div>
        </div>
    </div>
    
    <div class="game-over" id="gameOver">
        <h2>Игра окончена!</h2>
        <p>Финальный счет: <span id="finalScore">0</span></p>
        <button class="restart-btn" onclick="restartGame()">Начать заново</button>
    </div>

    <script src="./game.js"></script>
</body>
</html>
