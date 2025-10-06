async function postJSON(url,payload){ const res = await fetch(url,{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials:'include'}); return res.json(); }
export async function login(email,password){ return postJSON('/api/auth_login.php',{email,password}); }
export async function register(payload){ return postJSON('/api/auth_register.php', payload); }
export async function me(){ const r = await fetch('/api/me.php',{credentials:'include'}); return r.json(); }
export async function logout(){ const r = await fetch('/api/auth_logout.php',{method:'POST', credentials:'include'}); return r.json(); }
