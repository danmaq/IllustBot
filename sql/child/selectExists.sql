SELECT		COUNT(ID) > 0 AS EXIST
	FROM	IB01_INDEX_CHILD
	WHERE	ID = :id;