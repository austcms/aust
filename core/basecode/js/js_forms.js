function SenhaVerificaForm(entered){
	with(entered){
		if (frmsenhaatual.value == "" || frmsenhaatual.value == null){
			alert("Você não digitou a senha atual.\n Isto é necessário por razões de segurança.");
			return false;
		}
		if (frmnovasenha.value == "" || frmnovasenha.value == null){
			alert("Você não digitou a nova senha.");
			return false;
		}
		if (frmconfirmacao.value == "" || frmconfirmacao.value == null){
			alert("Você não digitou confirmação da senha.\n Isso é necessário para que evite que sejam digitadas palavras indesejadas.");
			return false;
		}
		if (frmconfirmacao.value != frmnovasenha.value){
			alert("A confirmação da senha não bate com a nova senha digitada.\n Redigite a nova senha e a confirmação da mesma.");
			return false;
		}
	}
}

function NoticiaVerificaForm(entered){
	with(entered){
		if (frmcategoria.value == "none" || frmcategoria.value == null){
			alert("Você não selecionou uma categoria para a notícia.");
			return false;
		}
		if (frmtitulo.value == "" || frmtitulo.value == null){
			alert("Você não digitou um título.");
			return false;
		}
		if (frmtexto.value == "" || frmtexto.value == null){
			alert("Você não digitou um texto.");
			return false;
		}
	}
}

function HistoriaVerificaForm(entered){
	with(entered){
		if (frmano.value == "none" || frmano.value == null || frmano.value == ""){
			var answer = confirm ("Você não estipulou uma data. \n Deseja continuar?")
			if (!answer)
				return false;
		}
	}
}

function EventoVerificaForm(entered){
	with(entered){
		if (frmcategoria.value == "none" || frmcategoria.value == null){
			alert("Você não selecionou uma categoria para o evento.");
			return false;
		}
		if (frmtitulo.value == "" || frmtitulo.value == null){
			alert("Você não digitou um título.");
			return false;
		}
		if (frmdia.value == "" || frmmes.value == "" || frmano.value == "" || frmhora.value == "" || frmminuto.value == ""){
			alert("Você não digitou uma data ou horário.");
			return false;
		}
	}
}

