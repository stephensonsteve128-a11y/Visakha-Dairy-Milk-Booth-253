const navToggle = document.querySelector(".nav__toggle");
const navLinks = document.querySelector(".nav__links");
const form = document.getElementById("orderForm");
const statusText = document.getElementById("formStatus");

navToggle.addEventListener("click", () => {
  navLinks.classList.toggle("active");
});

form.addEventListener("submit", async (event) => {
  event.preventDefault();
  statusText.textContent = "Sending your request...";

  const formData = new FormData(form);

  try {
    const response = await fetch("/api/submit_order.php", {
      method: "POST",
      body: formData,
    });

    if (!response.ok) {
      throw new Error("Unable to send the request.");
    }

    const result = await response.json();
    statusText.textContent = result.message || "Request received!";
    form.reset();
  } catch (error) {
    statusText.textContent = "We could not send your request. Please call the booth.";
  }
});
