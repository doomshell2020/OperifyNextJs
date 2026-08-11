const Sequelize = require('sequelize');
module.exports = function(sequelize, DataTypes) {
  return sequelize.define('emd_guarantees', {
    id: {
      autoIncrement: true,
      type: DataTypes.INTEGER,
      allowNull: false,
      primaryKey: true
    },
    bg_for: {
      type: DataTypes.STRING(200),
      allowNull: true
    },
    datefrom: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    bankguaranteeno: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    favour_of: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    po_no: {
      type: DataTypes.TEXT,
      allowNull: true
    },
    amount: {
      type: DataTypes.DECIMAL(12,2),
      allowNull: true
    },
    validupto: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    extenstionupto: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    lastdate: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    relese_date: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    po_or_rma: {
      type: DataTypes.ENUM('PO','RM'),
      allowNull: true
    },
    contect_per: {
      type: DataTypes.STRING(100),
      allowNull: true
    },
    created: {
      type: DataTypes.DATE,
      allowNull: true
    },
    updated: {
      type: DataTypes.DATE,
      allowNull: true
    },
    board_name: {
      type: DataTypes.STRING(255),
      allowNull: true
    },
    currency_type: {
      type: DataTypes.STRING(45),
      allowNull: true,
      defaultValue: "INR"
    },
    claim_upto: {
      type: DataTypes.DATEONLY,
      allowNull: true
    },
    status: {
      type: DataTypes.ENUM('Y','N'),
      allowNull: true,
      defaultValue: "N"
    },
    invoice_file: {
      type: DataTypes.TEXT,
      allowNull: true
    }
  }, {
    sequelize,
    tableName: 'emd_guarantees',
    timestamps: false,
    freezeTableName: true,
    indexes: [
      {
        name: "PRIMARY",
        unique: true,
        using: "BTREE",
        fields: [
          { name: "id" },
        ]
      },
    ]
  });
};
