async function jsonReq(url,data){ const res = await fetch(url,{method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data), credentials:'include'}); return res.json(); }
export async function getUnidades(){ const r = await fetch('/api/unidades_read.php',{credentials:'include'}); return r.json(); }
export async function createUnidade(data){ return jsonReq('/api/unidades_create.php', data); }
export async function updateUnidade(data){ return jsonReq('/api/unidades_update.php', data); }
export async function deleteUnidade(id){ return jsonReq('/api/unidades_delete.php', {id_unidade:id}); }
window.showAddUnidade = function(){ document.getElementById('addUnidadeForm').style.display='block'; }
window.addUnidade = async function(){ const sig=document.getElementById('unidadeSigla').value; const desc=document.getElementById('unidadeDesc').value; const resp = await createUnidade({sigla:sig,descricao:desc}); if(resp.success){ alert('Unidade criada'); location.reload(); } else alert('Erro'); }
window.loadUnidades = async function(){ const r = await getUnidades(); const t = document.getElementById('unidadesList'); if(!t) return; t.innerHTML=''; r.unidades.forEach(u=>{ const tr=document.createElement('tr'); tr.innerHTML=`<td>${u.id_unidade}</td><td>${u.sigla}</td><td>${u.descricao}</td><td><button class='btn btn-sm btn-warning' onclick="(async ()=>{ const s=prompt('Sigla', u.sigla); if(!s) return; await updateUnidade({id_unidade:u.id_unidade,sigla:s}); location.reload(); })()">Editar</button> <button class='btn btn-sm btn-danger' onclick="(async ()=>{ if(!confirm('Deletar?')) return; await deleteUnidade(${u.id_unidade}); location.reload(); })()">Apagar</button></td>`; t.appendChild(tr); }); }
window.addEventListener('DOMContentLoaded', window.loadUnidades);
