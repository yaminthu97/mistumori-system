// ダークモードトグルJS
$(".dark-mode-toggle").click(function () {
    var currentTheme = localStorage.getItem("theme");
    var notiList = document.querySelectorAll(".notification-list");
    const isDarkMode = currentTheme === "dark";

    document.documentElement.setAttribute(
        "data-theme",
        isDarkMode ? "light" : "dark"
    );
    localStorage.setItem("theme", isDarkMode ? "light" : "dark");

    // 通知を更新するBG色を編集
    notiList.forEach((notiItem) => {
        const itemBackground =
            window.getComputedStyle(notiItem).backgroundColor;

        if (isDarkMode) {
            notiItem.style.background =
                itemBackground === "rgb(15, 23, 42)" ? "#cbcbcb" : "#f5f5f5"; // light (not read, read)
        } else {
            notiItem.style.background =
                itemBackground === "rgb(203, 203, 203)" ? "#0f172a" : "#1E293B"; // dark (not read, read)
        }
    });

    document.documentElement.classList.toggle("dark");
});
