UPDATE		IB01_INDEX_CHILD
	SET		VOTE_COUNT	= :vote_count,
			SCORE		= :score
	WHERE	ID			= :id;
