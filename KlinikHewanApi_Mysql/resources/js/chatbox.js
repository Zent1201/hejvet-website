// Select the form, input, and chat container
const msgerForm = document.querySelector(".msger-inputarea");
const msgerInput = document.querySelector(".msger-input");
const msgerChat = document.querySelector(".msger-chat");

// Bot and user data
const BOT_MSGS = [
  "Hi, welcome to SimpleChat! How can I help you today? 😄",
  "I'm just a simple bot, but feel free to chat!",
  "Do you need any assistance?",
  "Remember, you can customize me further in the JS section!"
];

// Names and images for bot and user
const BOT_NAME = "drh. Blue Johnson, Sp.HKMU";
const USER_NAME = "Anda";
const BOT_IMG_URL = "url('images/doctors-1.jpg')"; // Bot image as a background URL
const USER_IMG = ""; // No image for user

// Add event listener for form submission
msgerForm.addEventListener("submit", event => {
  event.preventDefault();

  const msgText = msgerInput.value;
  if (!msgText) return;

  // Append user's message (without image)
  appendMessage(USER_NAME, USER_IMG, "right", msgText);
  msgerInput.value = "";

  // Simulate bot response after a delay
  botResponse();
});

// Function to append messages to the chat
function appendMessage(name, img, side, text) {
  // Check if it's the user message and remove the image for the user
  const imgHTML = img ? `<div class="msg-img" style="background-image: ${img}; background-size: cover; background-position: center;"></div>` : "";

  // Creates message HTML structure
  const msgHTML = `
    <div class="msg ${side}-msg">
      ${imgHTML}
      <div class="msg-bubble">
        <div class="msg-info">
          <div class="msg-info-name">${name}</div>
          <div class="msg-info-time">${formatDate(new Date())}</div>
        </div>
        <div class="msg-text">${text}</div>
      </div>
    </div>
  `;

  // Insert message into chat
  msgerChat.insertAdjacentHTML("beforeend", msgHTML);
  msgerChat.scrollTop += 500; // Scroll chat to the bottom
}

// Simulate a response from the bot
function botResponse() {
  const randomIndex = Math.floor(Math.random() * BOT_MSGS.length);
  const botText = BOT_MSGS[randomIndex];
  
  // Set a delay for the bot to "type" (1 second)
  setTimeout(() => {
    appendMessage(BOT_NAME, BOT_IMG_URL, "left", botText); // Use BOT_IMG_URL for bot image
  }, 1000);
}

// Utility function to format time for messages
function formatDate(date) {
  const hours = "0" + date.getHours();
  const minutes = "0" + date.getMinutes();
  return `${hours.slice(-2)}:${minutes.slice(-2)}`;
}
