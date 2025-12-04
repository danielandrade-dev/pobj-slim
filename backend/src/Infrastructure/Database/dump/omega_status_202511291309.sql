INSERT INTO omega_status (id,label,tone,descricao,ordem,departamento_id) VALUES
	 ('aberto','Aberto','neutral','Chamado aberto e aguardando atendimento.',1,'0'),
	 ('aguardando','Aguardando','warning','Chamado aguardando informações ou aprovação.',2,'0'),
	 ('cancelado','Cancelado','danger','Chamado cancelado.',5,'0'),
	 ('em_atendimento','Em atendimento','progress','Equipe atuando no chamado.',3,'0'),
	 ('resolvido','Resolvido','success','Chamado concluído.',4,'0');
