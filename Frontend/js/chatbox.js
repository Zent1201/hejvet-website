// Select the form, input, and chat container
const msgerForm = document.querySelector(".msger-inputarea");
const msgerInput = document.querySelector(".msger-input");
const msgerChat = document.querySelector(".msger-chat");

// Bot and user data (placeholders, will be updated dynamically)
let BOT_NAME = "drh. Blue Johnson, Sp.HKMU";
let BOT_IMG_URL = "images/default-bot.jpg"; // Default bot image
const USER_NAME = "Anda";
const USER_IMG = ""; // No image for user

// Fetch doctor data from API
const doctorId = 1; // Replace with dynamic ID if needed
fetch(`https://localhost:8000/api/users/${doctorId}`)
  .then((response) => response.json())
  .then((data) => {
    if (data && data.nama) {
      BOT_NAME = data.nama;
    }
    BOT_IMG_URL = data.foto_user ? data.foto_user : BOT_IMG_URL; // Use default if no image
  })
  .catch((error) => {
    console.error("Terjadi kesalahan saat mengambil data dokter:", error);
  });

// Add event listener for form submission
msgerForm.addEventListener("submit", (event) => {
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
function appendMessage(name, imgUrl, side, text) {
  const imgHTML = imgUrl
    ? `<img class="msg-img" src="${imgUrl}" alt="${name}"/>`
    : "";

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
  const BOT_MSGS = [
    "Hi, welcome to SimpleChat! How can I help you today? 😄",
    "I'm just a simple bot, but feel free to chat!",
    "Do you need any assistance?",
    "Remember, you can customize me further in the JS section!",
  ];
  const randomIndex = Math.floor(Math.random() * BOT_MSGS.length);
  const botText = BOT_MSGS[randomIndex];

  // Set a delay for the bot to "type" (1 second)
  setTimeout(() => {
    appendMessage(BOT_NAME, BOT_IMG_URL, "left", botText);
  }, 1000);
}

// Utility function to format time for messages
function formatDate(date) {
  const hours = "0" + date.getHours();
  const minutes = "0" + date.getMinutes();
  return `${hours.slice(-2)}:${minutes.slice(-2)}`;
}

