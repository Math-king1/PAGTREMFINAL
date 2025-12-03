async function buscarCEP(cepInputId, outputs={}){
  const cep = document.getElementById(cepInputId)?.value?.replace(/\D/g,'');
  if(!cep || cep.length !== 8){ alert('CEP inválido.'); return; }
  try{
    const res = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
    const data = await res.json();
    if(data.erro){ alert('CEP não encontrado.'); return; }
    if(outputs.city) document.getElementById(outputs.city).value = data.localidade || '';
    if(outputs.state) document.getElementById(outputs.state).value = data.uf || '';
  }catch(e){ alert('Falha ao consultar CEP.'); }
}
