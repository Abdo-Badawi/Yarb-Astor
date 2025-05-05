<?php
namespace Models;

class Review {
    public string $ReviewID;
    public string $SenderID;
    public int $ReceiverID;
    public string $OpportunityID;
    public float $Rating;
    public string $Comment;
    public bool $IsReported;
}
