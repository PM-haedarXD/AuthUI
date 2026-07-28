<div align="center">

<h1>🔐 AuthUI</h1>

<p><em>Secure authentication plugin for PocketMine-MP with beautiful UI forms</em></p>

<p>
  <img src="https://img.shields.io/badge/PocketMine--MP-5.0.0-fb8c00?style=for-the-badge&logo=github" alt="API">
  <img src="https://img.shields.io/badge/version-2.0.9-blue?style=for-the-badge" alt="Version">
  <img src="https://img.shields.io/badge/license-MIT-green?style=for-the-badge" alt="License">
  <img src="https://img.shields.io/badge/PHP-8.0%2B-777bb3?style=for-the-badge&logo=php" alt="PHP">
</p>

</div>

---

## 📸 Gallery

<p align="center"><em>Click any image to view full size</em></p>

<p align="center">
  <a href="screenshots/1.jpg"><img src="screenshots/1.jpg" width="180" style="border-radius:12px; margin:5px;" alt="Login Menu"></a>
  <a href="screenshots/2.jpg"><img src="screenshots/2.jpg" width="180" style="border-radius:12px; margin:5px;" alt="Register Form"></a>
  <a href="screenshots/3.jpg"><img src="screenshots/3.jpg" width="180" style="border-radius:12px; margin:5px;" alt="Change Password"></a>
  <a href="screenshots/4.jpg"><img src="screenshots/4.jpg" width="180" style="border-radius:12px; margin:5px;" alt="Frozen Player"></a>
  <a href="screenshots/5.jpg"><img src="screenshots/5.jpg" width="180" style="border-radius:12px; margin:5px;" alt="Login Success"></a>
</p>

<p align="center">
  🔑 Login &nbsp;|&nbsp;
  📝 Register &nbsp;|&nbsp;
  🔄 Change Password &nbsp;|&nbsp;
  ❄️ Frozen &nbsp;|&nbsp;
  ✅ Success
</p>

---

## 📖 Overview

> **AuthUI** protects your server with a modern UI-based authentication system. Players register and login through interactive forms — no commands needed.

| Feature | Description |
|--------|------------|
| 🔐 **Security** | UUID-bound accounts, password hashing ready |
| 🖥️ **UI Forms** | SimpleForm + CustomForm via libFormAPI |
| ❄️ **Freeze System** | Players frozen until authenticated |
| ⏱️ **Login Timeout** | Auto-kick after 20 minutes |
| 🌐 **Multi-language** | Supports PersianManager (optional) |
| 🛡️ **Anti-Cheat** | Blocks commands, movement, damage, inventory before login |

---

## 🧬 Core Methods

<strong>onEnable(): void</strong><br>
Initializes the plugin. Creates data folder and loads players.json database. Registers all event listeners.

<strong>onJoin(PlayerJoinEvent $e): void</strong><br>
Freezes the player immediately. Checks if account exists → shows login or register form. Starts 20-minute timeout.

<strong>openLoginMenu(Player $p): void</strong><br>
Opens a SimpleForm with two options: Login or Change Password.

<strong>loginForm(Player $p): void</strong><br>
CustomForm with password input. Validates against stored password. Calls authSuccess() on match.

<strong>registerForm(Player $p): void</strong><br>
CustomForm with password + confirm password. Creates new account with UUID binding.

<strong>changePasswordForm(Player $p): void</strong><br>
CustomForm with old password + new password + confirm. Validates old password before updating.

<strong>authSuccess(Player $p): void</strong><br>
Marks player as online, removes freeze after 3 seconds (60 ticks).

<strong>startLoginTimeout(Player $p): void</strong><br>
Kicks player if not authenticated within 20 minutes.

---

## 🔒 Security Events Blocked

| Event | Before Login |
|------|:---:|
| Block Break | ❌ |
| Block Place | ❌ |
| Movement | ❌ |
| Commands | ❌ |
| Damage | ❌ |
| Item Pickup | ❌ |
| Inventory | ❌ |

---

## 📥 Installation

| Step | Action |
|:---:|--------|
| 1 | Download AuthUI.phar from Releases |
| 2 | Place in plugins/ folder |
| 3 | Install <a href="https://github.com/jojoe77777/FormAPI">libFormAPI</a> |
| 4 | (Optional) Install PersianManager for Persian text |
| 5 | Restart server |

---

## ⚙️ Dependencies

| Plugin | Required | Link |
|--------|:-------:|------|
| FormAPI | ✅ Yes | <a href="https://github.com/jojoe77777/FormAPI">GitHub</a> |
| PersianManager | ❌ No | Optional |

---

## 💾 Data Format

<pre>
{
  "playername": {
    "password": "secret123",
    "uuid": "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
  }
}
</pre>

---

## 🔧 Troubleshooting

<strong>🔴 Class FormAPI not found</strong><br>
→ Install <a href="https://github.com/jojoe77777/FormAPI">libFormAPI</a> in plugins folder.

<strong>🔴 Class PersianManager not found</strong><br>
→ Ignore if not using Persian. Or install PersianManager plugin.

<strong>🔴 Account belongs to someone else</strong><br>
→ Players must use the same device/Xbox account. Delete their entry in players.json to reset.

---

## 📁 Project Structure

<pre>
AuthUI/
├── plugin.yml
├── src/
│   └── haedarXD/
│       └── Main.php
└── resources/
    └── players.json
</pre>

---

## 👤 Author

<div align="center">

<img src="https://github.com/PM-haedarXD.png" width="80" style="border-radius: 50%;">

### haedarXD

<a href="https://github.com/PM-haedarXD"><img src="https://img.shields.io/badge/GitHub-PM--haedarXD-24292e?style=flat-square&logo=github" alt="GitHub"></a>

📧 <a href="https://github.com/PM-haedarXD/AuthUI/issues">Report Bug</a> • 💡 <a href="https://github.com/PM-haedarXD/AuthUI/issues">Feature Request</a>

</div>

---

## 📜 License

<div align="center">

MIT — Free to use, modify, and distribute.

</div>

---

<div align="center">
  <sub>Made with ❤️ for the PocketMine community</sub>
</div>
