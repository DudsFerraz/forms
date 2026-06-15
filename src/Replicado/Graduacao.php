<?php

namespace Uspdev\Forms\Replicado;

use Illuminate\Support\Str;
use Uspdev\Replicado\DB;
use Uspdev\Replicado\Graduacao as GraduacaoReplicado;

class Graduacao extends GraduacaoReplicado
{
    /**
     * Procura disciplinas ativas de graduação pelo início do código.
     *
     * Derivado de Uspdev\Replicado\Graduacao::obterDisciplinas.
     *
     * @param string $coddis
     * @param int|null $limit
     * @return array
     */
    public static function procurarDisciplinas($coddis, $limit = 50)
    {
        $coddis = Str::upper(trim((string) $coddis));

        if (! preg_match('/^[A-Z0-9]+$/', $coddis)) {
            return [];
        }

        $limit = $limit === null ? 50 : max(1, min((int) $limit, 50));
        $queryLimit = "OFFSET 0 ROWS FETCH NEXT {$limit} ROWS ONLY";

        $query = "SELECT D1.*
                    FROM DISCIPLINAGR D1
                    INNER JOIN (
                        SELECT coddis, MAX(verdis) AS verdis
                        FROM DISCIPLINAGR
                        GROUP BY coddis
                    ) D2 ON D1.coddis = D2.coddis AND D1.verdis = D2.verdis
                    WHERE D1.coddis LIKE :coddis
                    AND D1.dtadtvdis IS NULL
                    AND D1.dtaatvdis IS NOT NULL
                    ORDER BY D1.coddis ASC
                    $queryLimit
        ";

        $disciplinas = DB::fetchAll($query, ['coddis' => $coddis . '%']);

        return is_array($disciplinas) ? $disciplinas : [];
    }
}
