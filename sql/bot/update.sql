UPDATE		IB01_INDEX_BOT
	SET		SCORE		= :score,
			GENERATION	= :generation
	WHERE	ENTITY_ID	= :entity_id;
