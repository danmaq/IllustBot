SELECT			ENTITY_ID
	FROM		IB01_INDEX_BOT
	WHERE		PUBLICATION = TRUE
	ORDER BY	SCORE		DESC,
				ADDED_ID	DESC
	LIMIT		:start, :length;
