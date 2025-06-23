Online Voting System - VoteSmart
A secure and user-friendly web-based application that allows administrators to create and manage
elections while enabling registered voters to cast their votes online. Built using PHP and MySQL,
this project is designed for transparency, simplicity, and accessibility.
Features:
- User Authentication Separate login for Admin and Voter roles.
- Election Management Create and manage upcoming and live elections.
- Candidate Management Add candidates with bio and photo.
- Voting System Voters can vote once per election.
- Live Results Admin can view results with vote counts and charts.
- Organized File Structure Easy to navigate and extend.


Technologies Used:
- PHP (Backend)
- MySQL (Database)
- HTML5 / CSS3 / JavaScript (Frontend)
- Chart.js (Results visualization)
Installation Guide:
1. Clone the Repository:
git clone https://github.com/Apsara770/online-voting-system.git
2. Import the Database:
- Open phpMyAdmin
- Create a DB: online_voting_system
- Import: sql/online_voting_system.sql
3. Update Database Config (in includes/db.php):
$host = "localhost";
$user = "root";
$pass = "";
$db = "online_voting_system";
Default Admin Login:
Email: admin@example.com
Password: admin123
Author:
Sanda
Undergraduate IT Student 2025
GitHub: https://github.com/Apsara770
License:
This project is open-source and free to use for educational purposes.
