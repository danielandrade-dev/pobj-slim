<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251203214954 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Adiciona índices para otimização de queries nas tabelas principais';
    }

    public function up(Schema $schema) : void
    {
        // Índices para f_realizados (tabela mais consultada)
        $this->addSql('CREATE INDEX idx_f_realizados_funcional ON f_realizados(funcional)');
        $this->addSql('CREATE INDEX idx_f_realizados_produto_id ON f_realizados(produto_id)');
        $this->addSql('CREATE INDEX idx_f_realizados_data_realizado ON f_realizados(data_realizado)');
        $this->addSql('CREATE INDEX idx_f_realizados_funcional_produto_data ON f_realizados(funcional, produto_id, data_realizado)');
        
        // Índices para f_meta
        $this->addSql('CREATE INDEX idx_f_meta_funcional ON f_meta(funcional)');
        $this->addSql('CREATE INDEX idx_f_meta_produto_id ON f_meta(produto_id)');
        $this->addSql('CREATE INDEX idx_f_meta_data_meta ON f_meta(data_meta)');
        $this->addSql('CREATE INDEX idx_f_meta_funcional_produto_data ON f_meta(funcional, produto_id, data_meta)');
        
        // Índices para f_pontos
        $this->addSql('CREATE INDEX idx_f_pontos_funcional ON f_pontos(funcional)');
        $this->addSql('CREATE INDEX idx_f_pontos_produto_id ON f_pontos(produto_id)');
        $this->addSql('CREATE INDEX idx_f_pontos_data_realizado ON f_pontos(data_realizado)');
        $this->addSql('CREATE INDEX idx_f_pontos_funcional_produto_data ON f_pontos(funcional, produto_id, data_realizado)');
        
        // Índices para f_detalhes
        $this->addSql('CREATE INDEX idx_f_detalhes_funcional ON f_detalhes(funcional)');
        $this->addSql('CREATE INDEX idx_f_detalhes_contrato_id ON f_detalhes(contrato_id)');
        
        // Índices para d_estrutura (filtros de hierarquia)
        $this->addSql('CREATE INDEX idx_d_estrutura_cargo_id ON d_estrutura(cargo_id)');
        $this->addSql('CREATE INDEX idx_d_estrutura_segmento_id ON d_estrutura(segmento_id)');
        $this->addSql('CREATE INDEX idx_d_estrutura_diretoria_id ON d_estrutura(diretoria_id)');
        $this->addSql('CREATE INDEX idx_d_estrutura_regional_id ON d_estrutura(regional_id)');
        $this->addSql('CREATE INDEX idx_d_estrutura_agencia_id ON d_estrutura(agencia_id)');
        $this->addSql('CREATE INDEX idx_d_estrutura_cargo_agencia ON d_estrutura(cargo_id, agencia_id)');
        
        // Índices para d_produtos (filtros de hierarquia de produtos)
        $this->addSql('CREATE INDEX idx_d_produtos_familia_id ON d_produtos(familia_id)');
        $this->addSql('CREATE INDEX idx_d_produtos_indicador_id ON d_produtos(indicador_id)');
        $this->addSql('CREATE INDEX idx_d_produtos_subindicador_id ON d_produtos(subindicador_id)');
    }

    public function down(Schema $schema) : void
    {
        // Remove índices de f_realizados
        $this->addSql('DROP INDEX idx_f_realizados_funcional ON f_realizados');
        $this->addSql('DROP INDEX idx_f_realizados_produto_id ON f_realizados');
        $this->addSql('DROP INDEX idx_f_realizados_data_realizado ON f_realizados');
        $this->addSql('DROP INDEX idx_f_realizados_funcional_produto_data ON f_realizados');
        
        // Remove índices de f_meta
        $this->addSql('DROP INDEX idx_f_meta_funcional ON f_meta');
        $this->addSql('DROP INDEX idx_f_meta_produto_id ON f_meta');
        $this->addSql('DROP INDEX idx_f_meta_data_meta ON f_meta');
        $this->addSql('DROP INDEX idx_f_meta_funcional_produto_data ON f_meta');
        
        // Remove índices de f_pontos
        $this->addSql('DROP INDEX idx_f_pontos_funcional ON f_pontos');
        $this->addSql('DROP INDEX idx_f_pontos_produto_id ON f_pontos');
        $this->addSql('DROP INDEX idx_f_pontos_data_realizado ON f_pontos');
        $this->addSql('DROP INDEX idx_f_pontos_funcional_produto_data ON f_pontos');
        
        // Remove índices de f_detalhes
        $this->addSql('DROP INDEX idx_f_detalhes_funcional ON f_detalhes');
        $this->addSql('DROP INDEX idx_f_detalhes_contrato_id ON f_detalhes');
        
        // Remove índices de d_estrutura
        $this->addSql('DROP INDEX idx_d_estrutura_cargo_id ON d_estrutura');
        $this->addSql('DROP INDEX idx_d_estrutura_segmento_id ON d_estrutura');
        $this->addSql('DROP INDEX idx_d_estrutura_diretoria_id ON d_estrutura');
        $this->addSql('DROP INDEX idx_d_estrutura_regional_id ON d_estrutura');
        $this->addSql('DROP INDEX idx_d_estrutura_agencia_id ON d_estrutura');
        $this->addSql('DROP INDEX idx_d_estrutura_cargo_agencia ON d_estrutura');
        
        // Remove índices de d_produtos
        $this->addSql('DROP INDEX idx_d_produtos_familia_id ON d_produtos');
        $this->addSql('DROP INDEX idx_d_produtos_indicador_id ON d_produtos');
        $this->addSql('DROP INDEX idx_d_produtos_subindicador_id ON d_produtos');
    }
}
