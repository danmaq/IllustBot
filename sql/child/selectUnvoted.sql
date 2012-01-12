SELECT			ID
	FROM		IB01_INDEX_CHILD
	WHERE		OWNER		= :owner		AND
				GENERATION	= :generation	AND
				VOTE_COUNT	= 0
	ORDER BY	RAND()
	LIMIT		1;
