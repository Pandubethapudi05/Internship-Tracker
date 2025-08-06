const express = require('express');const bodyParser = require('body-parser');const db = require('./database');const app = express();

app.use(bodyParser.json());
app.use(express.static('../frontend')); // Serve frontend static files
// Initialize databaseconst initDb = () => {
  const fs = require('fs');
  const schema = fs.readFileSync('./backend/schema.sql', 'utf8');
  db.exec(schema, (err) => {
    if (err) console.error('Error initializing DB:', err);
  });
};initDb();

// --- User registration ---
app.post('/api/register', (req, res) => {
  const { name, email, password, role } = req.body;
  const stmt = `INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)`;
  db.run(stmt, [name, email, password, role], function(err) {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ id: this.lastID });
  });
});
// --- User login ---
app.post('/api/login', (req, res) => {
  const { email, password } = req.body;
  db.get(`SELECT * FROM Users WHERE email = ? AND password = ?`, [email, password], (err, row) => {
    if (err || !row) return res.status(401).json({ error: 'Invalid credentials' });
    res.json({ user: row });
  });
});
// --- Create project (faculty) ---
app.post('/api/projects', (req, res) => {
  const { title, domain, duration, stipend, posted_by } = req.body;
  const stmt = `INSERT INTO Projects (title, domain, duration, stipend, posted_by) VALUES (?, ?, ?, ?, ?)`;
  db.run(stmt, [title, domain, duration, stipend, posted_by], function(err) {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ id: this.lastID });
  });
});
// --- Get all projects ---
app.get('/api/projects', (req, res) => {
  db.all(`SELECT Projects.*, Users.name as posted_by_name FROM Projects JOIN Users ON Projects.posted_by=Users.id`, [], (err, rows) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json(rows);
  });
});
// --- Apply to project (student) ---
app.post('/api/apply', (req, res) => {
  const { user_id, project_id } = req.body;
  const stmt = `INSERT INTO Applications (user_id, project_id, status) VALUES (?, ?, ?)`;
  db.run(stmt, [user_id, project_id, 'Applied'], function(err) {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ id: this.lastID });
  });
});
// --- Get applications for a project (faculty) ---
app.get('/api/applications/:project_id', (req, res) => {
  const { project_id } = req.params;
  db.all(`SELECT Applications.*, Users.name, Users.email FROM Applications JOIN Users ON Applications.user_id=Users.id WHERE project_id = ?`, [project_id], (err, rows) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json(rows);
  });
});
// --- Update application status ---
app.put('/api/application/:id', (req, res) => {
  const { id } = req.params;
  const { status } = req.body;
  db.run(`UPDATE Applications SET status = ? WHERE id = ?`, [status, id], function(err) {
    if (err) return res.status(500).json({ error: err.message });
    res.json({ message: 'Updated' });
  });
});
// --- Get projects posted by faculty ---
app.get('/api/myprojects/:user_id', (req, res) => {
  const { user_id } = req.params;
  db.all(`SELECT * FROM Projects WHERE posted_by = ?`, [user_id], (err, rows) => {
    if (err) return res.status(500).json({ error: err.message });
    res.json(rows);
  });
});

app.listen(3000, () => console.log('Server listening on port 3000'));

