SELECT			COUNT(ID) AS COUNT
	FROM		IB01_INDEX_CHILD
	WHERE		OWNER		= :owner		AND
				GENERATION	= :generation
	ORDER BY	SCORE;
