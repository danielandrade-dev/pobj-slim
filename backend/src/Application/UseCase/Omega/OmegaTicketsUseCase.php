<?php

namespace App\Application\UseCase\Omega;

use App\Entity\Omega\OmegaChamado;
use App\Repository\Omega\OmegaChamadoRepository;
use App\Repository\Omega\OmegaStatusRepository;
use App\Repository\Omega\OmegaDepartamentoRepository;
use App\Repository\Omega\OmegaUsuarioRepository;

/**
 * UseCase para operações relacionadas a tickets Omega
 */
class OmegaTicketsUseCase
{
    private $repository;
    private $statusRepository;
    private $departamentoRepository;
    private $usuarioRepository;

    public function __construct(
        OmegaChamadoRepository $repository,
        OmegaStatusRepository $statusRepository,
        OmegaDepartamentoRepository $departamentoRepository,
        OmegaUsuarioRepository $usuarioRepository
    ) {
        $this->repository = $repository;
        $this->statusRepository = $statusRepository;
        $this->departamentoRepository = $departamentoRepository;
        $this->usuarioRepository = $usuarioRepository;
    }

    /**
     * Retorna todos os tickets Omega
     * @return array
     */
    public function getAllTickets(): array
    {
        return $this->repository->findAllOrderedByUpdated();
    }

    public function handle($filters = null): array
    {
        return $this->getAllTickets();
    }

    /**
     * Cria um novo ticket Omega
     * @param array $data
     * @return OmegaChamado
     */
    public function createTicket(array $data): OmegaChamado
    {
        $ticket = new OmegaChamado();
        
        // Gera ID único através do repository
        $ticket->setId($this->repository->generateNextTicketId());
        
        // Mapeia dados básicos
        $this->mapBasicData($ticket, $data);
        
        // Mapeia relacionamentos
        $this->mapRelationships($ticket, $data);
        
        // Mapeia contexto e histórico
        $this->mapContextAndHistory($ticket, $data);
        
        // Salva através do repository
        $this->repository->save($ticket);
        
        return $ticket;
    }

    /**
     * Mapeia dados básicos do ticket
     */
    private function mapBasicData(OmegaChamado $ticket, array $data): void
    {
        $ticket->setSubject($data['subject'] ?? 'Chamado Omega');
        $ticket->setCompany($data['company'] ?? null);
        $ticket->setProductId($data['productId'] ?? null);
        $ticket->setProductLabel($data['product'] ?? null);
        $ticket->setFamily($data['family'] ?? null);
        $ticket->setSection($data['section'] ?? null);
        $ticket->setQueue($data['queue'] ?? null);
        $ticket->setCategory($data['category'] ?? null);
        $ticket->setPriority($data['priority'] ?? 'media');
        
        $now = new \DateTime();
        $ticket->setOpened($now);
        $ticket->setUpdated($now);
        
        if (isset($data['dueDate']) && $data['dueDate']) {
            $ticket->setDueDate(new \DateTime($data['dueDate']));
        }
    }

    /**
     * Mapeia relacionamentos (status, requester, owner, team)
     */
    private function mapRelationships(OmegaChamado $ticket, array $data): void
    {
        // Status
        $statusLabel = $data['status'] ?? 'aberto';
        $status = $this->statusRepository->findByLabel($statusLabel) 
            ?? $this->statusRepository->findByLabel('aberto');
        $ticket->setStatus($status);
        
        // Requester
        if (isset($data['requesterId'])) {
            $requester = $this->usuarioRepository->find($data['requesterId']);
            $ticket->setRequester($requester);
        }
        
        // Owner
        if (isset($data['ownerId']) && $data['ownerId']) {
            $owner = $this->usuarioRepository->find($data['ownerId']);
            $ticket->setOwner($owner);
        }
        
        // Team (departamento)
        if (isset($data['teamId']) && $data['teamId']) {
            $team = $this->departamentoRepository->find($data['teamId']);
            $ticket->setTeam($team);
        } elseif (isset($data['queue']) && $data['queue']) {
            $team = $this->departamentoRepository->findByNome($data['queue']);
            $ticket->setTeam($team);
        }
    }

    /**
     * Mapeia contexto organizacional e histórico
     */
    private function mapContextAndHistory(OmegaChamado $ticket, array $data): void
    {
        // Contexto organizacional
        if (isset($data['context'])) {
            $context = $data['context'];
            $ticket->setDiretoria($context['diretoria'] ?? null);
            $ticket->setGerencia($context['gerencia'] ?? null);
            $ticket->setAgencia($context['agencia'] ?? null);
            $ticket->setGerenteGestao($context['ggestao'] ?? null);
            $ticket->setGerente($context['gerente'] ?? null);
        }
        
        // Histórico
        if (isset($data['history']) && is_array($data['history'])) {
            $historyData = $this->formatHistory($data['history']);
            $ticket->setHistory(json_encode($historyData, JSON_UNESCAPED_UNICODE));
        } elseif (isset($data['observation'])) {
            $historyData = [[
                'date' => (new \DateTime())->format('Y-m-d\TH:i:s\Z'),
                'action' => 'Abertura do chamado',
                'comment' => $data['observation']
            ]];
            $ticket->setHistory(json_encode($historyData, JSON_UNESCAPED_UNICODE));
        }
    }

    /**
     * Atualiza um ticket existente
     * @param string $id
     * @param array $data
     * @return OmegaChamado|null
     */
    public function updateTicket(string $id, array $data): ?OmegaChamado
    {
        $ticket = $this->repository->find($id);
        
        if (!$ticket) {
            return null;
        }
        
        // Atualiza dados básicos se fornecidos
        if (isset($data['subject'])) {
            $ticket->setSubject($data['subject']);
        }
        
        if (isset($data['priority'])) {
            $ticket->setPriority($data['priority']);
        }
        
        if (isset($data['queue'])) {
            $ticket->setQueue($data['queue']);
        }
        
        if (isset($data['category'])) {
            $ticket->setCategory($data['category']);
        }
        
        if (isset($data['dueDate'])) {
            $ticket->setDueDate($data['dueDate'] ? new \DateTime($data['dueDate']) : null);
        }
        
        // Atualiza data de atualização
        if (isset($data['updated'])) {
            try {
                $ticket->setUpdated(new \DateTime($data['updated']));
            } catch (\Exception $e) {
                $ticket->setUpdated(new \DateTime());
            }
        } else {
            $ticket->setUpdated(new \DateTime());
        }
        
        // Atualiza status se fornecido
        if (isset($data['status'])) {
            // Tenta encontrar por ID primeiro, depois por label
            $status = $this->statusRepository->find($data['status']);
            if (!$status) {
                // Se não encontrou por ID, tenta por label
                $status = $this->statusRepository->findByLabel($data['status']);
            }
            if ($status) {
                $ticket->setStatus($status);
            }
        }
        
        // Atualiza relacionamentos
        if (isset($data['requesterId'])) {
            $requester = $this->usuarioRepository->find($data['requesterId']);
            if ($requester) {
                $ticket->setRequester($requester);
            }
        }
        
        if (isset($data['ownerId'])) {
            $owner = $data['ownerId'] ? $this->usuarioRepository->find($data['ownerId']) : null;
            $ticket->setOwner($owner);
        }
        
        if (isset($data['teamId'])) {
            $team = $data['teamId'] ? $this->departamentoRepository->find($data['teamId']) : null;
            $ticket->setTeam($team);
        }
        
        // Atualiza histórico se fornecido
        if (isset($data['history']) && is_array($data['history'])) {
            $historyData = $this->formatHistory($data['history']);
            $ticket->setHistory(json_encode($historyData, JSON_UNESCAPED_UNICODE));
        }
        
        // Atualiza contexto se fornecido
        if (isset($data['context']) && is_array($data['context'])) {
            $context = $data['context'];
            if (isset($context['diretoria'])) {
                $ticket->setDiretoria($context['diretoria']);
            }
            if (isset($context['gerencia'])) {
                $ticket->setGerencia($context['gerencia']);
            }
            if (isset($context['agencia'])) {
                $ticket->setAgencia($context['agencia']);
            }
            if (isset($context['ggestao'])) {
                $ticket->setGerenteGestao($context['ggestao']);
            }
            if (isset($context['gerente'])) {
                $ticket->setGerente($context['gerente']);
            }
        }
        
        // Salva as alterações
        $this->repository->save($ticket);
        
        return $ticket;
    }

    /**
     * Formata array de histórico para estrutura padronizada
     */
    private function formatHistory(array $history): array
    {
        $formatted = [];
        foreach ($history as $entry) {
            $formatted[] = [
                'date' => $entry['date'] ?? null,
                'actorId' => $entry['actorId'] ?? null,
                'action' => $entry['action'] ?? 'Abertura do chamado',
                'comment' => $entry['comment'] ?? '',
                'status' => $entry['status'] ?? 'aberto'
            ];
        }
        return $formatted;
    }
    
    /**
     * Converte entidade OmegaChamado para array
     * @param OmegaChamado $ticket
     * @return array
     */
    public function ticketToArray(OmegaChamado $ticket): array
    {
        $status = $ticket->getStatus();
        $requester = $ticket->getRequester();
        $owner = $ticket->getOwner();
        $team = $ticket->getTeam();
        
        return [
            'id' => $ticket->getId(),
            'subject' => $ticket->getSubject(),
            'company' => $ticket->getCompany(),
            'product_id' => $ticket->getProductId(),
            'product_label' => $ticket->getProductLabel(),
            'family' => $ticket->getFamily(),
            'section' => $ticket->getSection(),
            'queue' => $ticket->getQueue(),
            'category' => $ticket->getCategory(),
            'status' => $status ? $status->getId() : null,
            'priority' => $ticket->getPriority(),
            'opened' => $ticket->getOpened() ? $ticket->getOpened()->format('Y-m-d\TH:i:s\Z') : null,
            'updated' => $ticket->getUpdated() ? $ticket->getUpdated()->format('Y-m-d\TH:i:s\Z') : null,
            'due_date' => $ticket->getDueDate() ? $ticket->getDueDate()->format('Y-m-d\TH:i:s\Z') : null,
            'requester_id' => $requester ? $requester->getId() : null,
            'owner_id' => $owner ? $owner->getId() : null,
            'team_id' => $team ? $team->getDepartamentoId() : null,
            'history' => $ticket->getHistory(),
            'diretoria' => $ticket->getDiretoria(),
            'gerencia' => $ticket->getGerencia(),
            'agencia' => $ticket->getAgencia(),
            'gerente_gestao' => $ticket->getGerenteGestao(),
            'gerente' => $ticket->getGerente(),
            'credit' => $ticket->getCredit(),
            'attachment' => $ticket->getAttachment()
        ];
    }
}

