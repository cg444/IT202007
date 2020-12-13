// Collect The Square game

// Get a reference to the canvas DOM element
var canvas = document.getElementById('box');
// Get the canvas drawing context
var context = canvas.getContext('2d');

// Your score
var score = 0;

// Properties for your square
var x = 50; // X position
var y = 100; // Y position
var speed = 6; // Distance to move each frame
var sideLength = 50; // Length of each side of the square

// FLags to track which keys are pressed
var down = false;
var up = false;
var right = false;
var left = false;

// Properties for the target square
var targetX = 0;
var targetY = 0;
var targetLength = 25;

//Properties of the time square
var timeSqX = 0;
var timeSqY = 0;
var timeSqLength = 25;

// Determine if number a is within the range b to c (exclusive)
function isWithin(a, b, c) {
    return (a > b && a < c);
}

// Countdown timer (in seconds)
var countdown = 30;
// ID to track the setTimeout
var id = null;

// Listen for keydown events
window.addEventListener('keydown', function(event) {
    event.preventDefault();
    console.log(event.key, event.keyCode);
    if (event.keyCode === 40) { // DOWN
        down = true;
    }
    if (event.keyCode === 38) { // UP
        up = true;
    }
    if (event.keyCode === 37) { // LEFT
        left = true;
    }
    if (event.keyCode === 39) { // RIGHT
        right = true;
    }
});

// Listen for keyup events
window.addEventListener('keyup', function(event) {
    event.preventDefault();
    console.log(event.key, event.keyCode);
    if (event.keyCode === 40) { // DOWN
        down = false;
    }
    if (event.keyCode === 38) { // UP
        up = false;
    }
    if (event.keyCode === 37) { // LEFT
        left = false;
    }
    if (event.keyCode === 39) { // RIGHT
        right = false;
    }
});

// Show the start menu
function menu() {
    erase();
    context.fillStyle = '#000000';
    context.font = '36px Arial';
    context.textAlign = 'center';
    context.fillText('Collect the Square!', canvas.width / 2, canvas.height / 4);
    context.font = '24px Arial';
    context.fillText('Click to Start', canvas.width / 2, canvas.height / 2);
    context.font = '18px Arial'
    context.fillText('Use the arrow keys to move', canvas.width / 2, (canvas.height / 4) * 3);
    // Start the game on a click
    window.addEventListener('click', startGame);
}

// Start the game
function startGame() {
    // Reduce the countdown timer ever second
    id = setInterval(function() {
        countdown--;
    }, 1000)
    // Stop listening for click events
    window.removeEventListener('click', startGame);
    // Put the target at a random starting point
    moveTarget();
    // Kick off the draw loop
    draw();
}
// Toggle background colors
var myVar = setInterval(setColor, 1000);

function setColor() {
    var x = document.body;
    x.style.backgroundColor = x.style.backgroundColor == "blue" ? "green" : "blue";
}

function stopColor() {
    clearInterval(myVar);
}

// Show the game over screen
function endGame() {
    // Stop the countdown
    clearInterval(id);
    // Display the final score
    erase();
    context.fillStyle = '#000000';
    context.font = '24px Arial';
    context.textAlign = 'center';
    context.fillText('Final Score: ' + score, canvas.width / 2, canvas.height / 2);
}

// Move the target square to a random position
function moveTarget() {
    targetX = Math.round(Math.random() * canvas.width - targetLength);
    targetY = Math.round(Math.random() * canvas.height - targetLength)
}
function moveTimeSquare() {
    timeSqX = Math.round(Math.random() * canvas.width - targetLength);
    timeSqY = Math.round(Math.random() * canvas.height - targetLength)
}

// Clear the canvas
function erase() {
    context.fillStyle = '#FFFFFF';
    context.fillRect(0, 0, 600, 400);
}

// The main draw loop
function draw() {
    erase();
    // Move the square
    if (down) {
        y += speed;
    }
    if (up) {
        y -= speed;
    }
    if (right) {
        x += speed;
    }
    if (left) {
        x -= speed;
    }
    // Keep the square within the bounds
    if (y + sideLength > canvas.height) {
        y = canvas.height - sideLength;
    }
    if (y < 0) {
        y = 0;
    }
    if (x < 0) {
        x = 0;
    }
    if (x + sideLength > canvas.width) {
        x = canvas.width - sideLength;
    }
    //Collide with TimeSquare
    if (isWithin(timeSqX, x, x + sideLength) || isWithin(timeSqX + timeSqLength, x, x + sideLength)) { // X
        if (isWithin(timeSqY, y, y + sideLength) || isWithin(timeSqY + timeSqLength, y, y + timeSqLength)) { // Y
            // Respawn the target
            moveTimeSquare();
            // Increase the countdown
            countdown+=5;
            //Increase size of square
            sideLength-=5;
        }
    }
    // Collide with the target
    if (isWithin(targetX, x, x + sideLength) || isWithin(targetX + targetLength, x, x + sideLength)) { // X
        if (isWithin(targetY, y, y + sideLength) || isWithin(targetY + targetLength, y, y + sideLength)) { // Y
            // Respawn the target
            moveTarget();
            // Increase the score
            score++;
            //Increase size of square
            sideLength+=5;
        }

    }
    // Draw the square
    context.fillStyle = '#FF0000';
    context.fillRect(x, y, sideLength, sideLength);
    // Draw the target
    context.fillStyle = '#00FF00';
    context.fillRect(targetX, targetY, targetLength, targetLength);
    //Draw the TimeSquare
    context.fillStyle = '#0000FF';
    context.fillRect(timeSqX, timeSqY, timeSqLength, timeSqLength);
    // Draw the score and time remaining
    context.fillStyle = '#000000';
    context.font = '24px Arial';
    context.textAlign = 'left';
    context.fillText('Score: ' + score, 10, 24);
    context.fillText('Time Remaining: ' + countdown, 10, 50);
    //speed increase at set times
    if (countdown%5 == 0) {
        speed++;
    }
    // End the game or keep playing
    if (countdown <= 0) {
        endGame();
    } else {
        window.requestAnimationFrame(draw);
    }
}

// Start the game
menu();
canvas.focus();